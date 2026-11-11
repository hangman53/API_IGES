CREATE TABLE IF NOT EXISTS usuarios (
    id          SERIAL PRIMARY KEY,
    username    VARCHAR(100)  NOT NULL,
    email       VARCHAR(255)  UNIQUE NOT NULL,
    senha_hash  VARCHAR(255)  NOT NULL,
    role        VARCHAR(50)   NOT NULL DEFAULT 'usuario',
    token       VARCHAR(64),
    criado_em   TIMESTAMP     NOT NULL DEFAULT NOW()
);
