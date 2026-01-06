-- USUÁRIOS
INSERT INTO usuarios (
    nome,
    email,
    celular,
    senha,
    usuario_status_id,
    nivel_acesso_id,
    created
  )
VALUES (
    "Carlos Eduardo",
    "contato@agenciardmind.com.br",
    "5511965828725",
    "$2y$10$jD0vDCsXc3/W324EtRsy3uwKLa3tFMeLCVgRVrgPIBdB0emXc17S.",
    3,
    2,
    NOW()
  ),
  (
    "John Doe",
    "cadu.diasprado07@gmail.com",
    "5511965828725",
    "$2y$10$jD0vDCsXc3/W324EtRsy3uwKLa3tFMeLCVgRVrgPIBdB0emXc17S.",
    3,
    1,
    NOW()
  ),
  (
    "Johana Doe",
    "cadu.devmarketing@gmail.com",
    "5511965828725",
    "$2y$10$jD0vDCsXc3/W324EtRsy3uwKLa3tFMeLCVgRVrgPIBdB0emXc17S.",
    1,
    1,
    NOW()
  );

-- PRODUTOS
INSERT INTO produtos (nome)
VALUES
("Sites"),
("Emails");

-- EQUIPES
INSERT INTO equipes (nome, produto_id, equipe_status_id, created)
VALUES 
("Vendas", 1, 3, NOW()),
("Orgânico", 1, 3, NOW());

-- LEADS

-- ATENDIMENTOS