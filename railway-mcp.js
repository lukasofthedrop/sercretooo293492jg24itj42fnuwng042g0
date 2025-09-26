const express = require('express');
const axios = require('axios');
const { exec } = require('child_process');
const path = require('path');
const fs = require('fs');

const app = express();
const PORT = process.env.PORT || 3001;

// Middleware
app.use(express.json());
app.use(express.static(path.join(__dirname, 'public')));

// Railway API configuration
const RAILWAY_API_BASE = 'https://backboard.railway.app/graphql/v2';

// Helper function to execute shell commands
function executeCommand(command, options = {}) {
    return new Promise((resolve, reject) => {
        exec(command, options, (error, stdout, stderr) => {
            if (error) {
                reject(error);
                return;
            }
            resolve({ stdout, stderr });
        });
    });
}

// Health check endpoint
app.get('/health', (req, res) => {
    res.json({
        status: 'healthy',
        timestamp: new Date().toISOString(),
        service: 'lucrativabet-railway-mcp',
        version: '1.0.0'
    });
});

// Get Railway project status
app.get('/api/railway/status', async (req, res) => {
    try {
        const { stdout } = await executeCommand('railway status --json');
        const status = JSON.parse(stdout);
        res.json(status);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// Get Railway logs
app.get('/api/railway/logs', async (req, res) => {
    try {
        const { service } = req.query;
        let command = 'railway logs';
        if (service) {
            command += ` --service ${service}`;
        }
        const { stdout } = await executeCommand(command);
        res.json({ logs: stdout });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// Get environment variables
app.get('/api/railway/env', async (req, res) => {
    try {
        const { stdout } = await executeCommand('railway variables --json');
        const envVars = JSON.parse(stdout);
        res.json(envVars);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// Set environment variable
app.post('/api/railway/env', async (req, res) => {
    try {
        const { name, value } = req.body;
        if (!name || value === undefined) {
            return res.status(400).json({ error: 'Name and value are required' });
        }
        await executeCommand(`railway variables:set ${name}="${value}"`);
        res.json({ success: true, message: `Environment variable ${name} set successfully` });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// Restart service
app.post('/api/railway/restart', async (req, res) => {
    try {
        const { service } = req.body;
        let command = 'railway restart';
        if (service) {
            command += ` --service ${service}`;
        }
        await executeCommand(command);
        res.json({ success: true, message: 'Service restarted successfully' });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// Deploy to Railway
app.post('/api/railway/deploy', async (req, res) => {
    try {
        const { stdout } = await executeCommand('railway up');
        res.json({ success: true, message: 'Deployment initiated', output: stdout });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// Get project information
app.get('/api/railway/project', async (req, res) => {
    try {
        const { stdout } = await executeCommand('railway project --json');
        const project = JSON.parse(stdout);
        res.json(project);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// Get services information
app.get('/api/railway/services', async (req, res) => {
    try {
        const { stdout } = await executeCommand('railway services --json');
        const services = JSON.parse(stdout);
        res.json(services);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// Get domains information
app.get('/api/railway/domains', async (req, res) => {
    try {
        const { stdout } = await executeCommand('railway domains --json');
        const domains = JSON.parse(stdout);
        res.json(domains);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// Webhook for Railway events
app.post('/api/railway/webhook', (req, res) => {
    const event = req.body;
    console.log('Received Railway webhook event:', event);
    
    // Handle different event types
    switch (event.type) {
        case 'deployment.succeeded':
            console.log('Deployment succeeded');
            // Add your logic here
            break;
        case 'deployment.failed':
            console.log('Deployment failed');
            // Add your logic here
            break;
        default:
            console.log('Unhandled event type:', event.type);
    }
    
    res.json({ received: true });
});

// Dashboard route
app.get('/', (req, res) => {
    res.send(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>LucrativaBet Railway MCP Dashboard</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; }
                .container { max-width: 1200px; margin: 0 auto; }
                .card { border: 1px solid #ddd; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
                .btn { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
                .btn:hover { background: #0056b3; }
                .status { padding: 5px 10px; border-radius: 4px; display: inline-block; }
                .status.healthy { background: #d4edda; color: #155724; }
                .status.unhealthy { background: #f8d7da; color: #721c24; }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>LucrativaBet Railway MCP Dashboard</h1>
                <div class="card">
                    <h2>Service Status</h2>
                    <div id="status">Loading...</div>
                </div>
                <div class="card">
                    <h2>Actions</h2>
                    <button class="btn" onclick="deploy()">Deploy</button>
                    <button class="btn" onclick="restart()">Restart Services</button>
                    <button class="btn" onclick="getLogs()">View Logs</button>
                </div>
                <div class="card">
                    <h2>Recent Logs</h2>
                    <pre id="logs">Loading...</pre>
                </div>
            </div>
            <script>
                async function getStatus() {
                    try {
                        const response = await fetch('/api/railway/status');
                        const data = await response.json();
                        document.getElementById('status').innerHTML = \`
                            <div class="status healthy">Healthy</div>
                            <p>Project: \${data.project}</p>
                            <p>Environment: \${data.environment}</p>
                        \`;
                    } catch (error) {
                        document.getElementById('status').innerHTML = '<div class="status unhealthy">Error</div>';
                    }
                }
                
                async function deploy() {
                    try {
                        const response = await fetch('/api/railway/deploy', { method: 'POST' });
                        const data = await response.json();
                        alert(data.message);
                    } catch (error) {
                        alert('Deployment failed: ' + error.message);
                    }
                }
                
                async function restart() {
                    try {
                        const response = await fetch('/api/railway/restart', { method: 'POST' });
                        const data = await response.json();
                        alert(data.message);
                    } catch (error) {
                        alert('Restart failed: ' + error.message);
                    }
                }
                
                async function getLogs() {
                    try {
                        const response = await fetch('/api/railway/logs');
                        const data = await response.json();
                        document.getElementById('logs').textContent = data.logs;
                    } catch (error) {
                        document.getElementById('logs').textContent = 'Error loading logs';
                    }
                }
                
                // Load initial data
                getStatus();
                getLogs();
                
                // Refresh every 30 seconds
                setInterval(() => {
                    getStatus();
                    getLogs();
                }, 30000);
            </script>
        </body>
        </html>
    `);
});

// Start server
app.listen(PORT, () => {
    console.log(`LucrativaBet Railway MCP server running on port ${PORT}`);
});

// Export for testing
module.exports = app;