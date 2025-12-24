import { criarUsuario } from "./criar-usuario";
import { desativar } from "./desativar";
import { editarUsuario } from "./editar-usuario";
import { reativarUsuario } from "./reativar";
import { reenviarEmail } from "./reenviar-email";
import { resetarSenha } from "./resetar-senha";

export default {
  "usuario:criar": criarUsuario,
  "usuario:editar": editarUsuario,
  "usuario:reenviar-email": reenviarEmail,
  "usuario:desativar": desativar,
  "usuario:reativar": reativarUsuario,
  "usuario:resetar-senha": resetarSenha
}