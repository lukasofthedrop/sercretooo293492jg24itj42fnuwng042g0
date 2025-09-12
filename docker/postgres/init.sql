-- Inicialização do banco de dados LucrativaBet
-- Este script será executado quando o container PostgreSQL for iniciado

-- Criar extensões necessárias
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
CREATE EXTENSION IF NOT EXISTS "pg_trgm";

-- Inserir configurações básicas (se necessário)
-- INSERT INTO settings (key, value, created_at, updated_at) VALUES 
-- ('site_name', 'Lucrativa Bet', NOW(), NOW()),
-- ('site_description', 'Melhor cassino online e apostas esportivas', NOW(), NOW())
-- ON CONFLICT (key) DO NOTHING;