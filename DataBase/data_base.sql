BEGIN;

DROP TABLE IF EXISTS usuarios;
CREATE TABLE IF NOT EXISTS usuarios (
    id  smallserial unique primary key,
    nome varchar(100) not null,
    cnpj varchar(14) unique,
    cpf varchar(11) unique,
    email varchar(50) unique not null,
    senha text
);

DROP TABLE IF EXISTS transacoes;
DROP TABLE IF EXISTS tipo_transacao;
CREATE TABLE IF NOT EXISTS tipo_transacao (
    id smallint unique primary key,
    descricao varchar(50)
);

DROP TABLE IF EXISTS conta;
CREATE TABLE IF NOT EXISTS conta (
    id smallserial unique unique primary key,
    id_usuario smallint not null,
    saldo decimal(18, 8)
);

CREATE TABLE IF NOT EXISTS transacoes (
    id bigserial unique primary key,
    tipo smallint,
    id_conta smallint,
    data timestamp not null,
    valor decimal(18, 8),
    ativa boolean default(true),
    data_estorno timestamp,
    id_transcao_estorno bigint,

    FOREIGN KEY (tipo) REFERENCES tipo_transacao(id),
    FOREIGN KEY (id_conta) REFERENCES conta(id)
);

COMMIT;