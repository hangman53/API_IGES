CREATE TABLE IF NOT EXISTS respostas_teste_preliminar (
    usuario_id     INTEGER   PRIMARY KEY REFERENCES usuarios(id) ON DELETE CASCADE,
    respostas      JSONB     NOT NULL,
    respondido_em  TIMESTAMP NOT NULL DEFAULT NOW()
);
