module.exports = {
  apps: [{
    name: 'lucrativabet',
    script: 'php artisan serve',
    args: '--host=0.0.0.0 --port=8000',
    cwd: '/Users/rkripto/Downloads/lucrativabet',
    autorestart: true,
    watch: false,
    max_memory_restart: '1G',
    env: {
      NODE_ENV: 'production',
      APP_ENV: 'production'
    },
    env_production: {
      NODE_ENV: 'production',
      APP_ENV: 'production'
    }
  }]
};