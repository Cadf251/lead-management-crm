import { alterarFuncao } from "./alterar-funcao";
import { alterarRecebimento } from "./alterar-recebimento";
import { ativarEquipe } from "./ativar-equipe";
import { prejudicar } from "./colaborador-prejudicar";
import { priorizar } from "./colaborador-priorizar";
import { criarEquipe } from "./criar-equipe";
import { desativarEquipe } from "./desativar-equipe";
import { editarEquipe } from "./editar-equipe";
import { novoColaborador } from "./novo-colaborador";
import { pausarEquipe } from "./pausar-equipe";
import { remover } from "./remover-colaborador";

export default {
  "equipe:criar": criarEquipe,
  "equipe:editar": editarEquipe,
  "equipe:desativar": desativarEquipe,
  "equipe:pausar": pausarEquipe,
  "equipe:ativar": ativarEquipe,
  "colaborador:novo": novoColaborador,
  "colaborador:alterar-funcao": alterarFuncao,
  "colaborador:recebimento": alterarRecebimento,
  "colaborador:prejudicar": prejudicar,
  "colaborador:priorizar": priorizar,
  "colaborador:remover": remover
}