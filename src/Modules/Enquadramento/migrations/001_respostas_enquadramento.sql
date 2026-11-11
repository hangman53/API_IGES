CREATE TABLE IF NOT EXISTS respostas_enquadramento (
    usuario_id       INTEGER   NOT NULL REFERENCES usuarios(id) ON DELETE CASCADE,
    instrumento      SMALLINT  NOT NULL CHECK (instrumento BETWEEN 1 AND 23),
    codigo_pergunta  SMALLINT  NOT NULL,
    nota             SMALLINT  NOT NULL CHECK (nota BETWEEN 0 AND 4),
    respondido_em    TIMESTAMP NOT NULL DEFAULT NOW(),
    PRIMARY KEY (usuario_id, instrumento, codigo_pergunta)
);
