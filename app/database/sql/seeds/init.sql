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
    4,
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

-- LEADS

-- ATENDIMENTOS