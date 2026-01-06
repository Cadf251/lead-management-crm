UPDATE usuarios
SET nivel_acesso_id = 1
WHERE nivel_acesso_id = 2 OR nivel_acesso_id = 3;

UPDATE usuarios
SET nivel_acesso_id = 2
WHERE nivel_acesso_id = 4;

DELETE FROM niveis_acesso
WHERE id = 3 OR id = 4;

UPDATE niveis_acesso
SET
  nome = "Administrador",
  descricao = "Possui acesso completo a todas as funcionalidades e configurações do sistema."
WHERE id = 2;