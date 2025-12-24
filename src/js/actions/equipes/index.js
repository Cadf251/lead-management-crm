import { ativarEquipe } from "./ativar-equipe";
import { criarEquipe } from "./criar-equipe";
import { desativarEquipe } from "./desativar-equipe";
import { editarEquipe } from "./editar-equipe";
import { novoColaborador } from "./novo-colaborador";
import { pausarEquipe } from "./pausar-equipe";

export default {
  "equipe:criar": criarEquipe,
  "equipe:editar": editarEquipe,
  "equipe:desativar": desativarEquipe,
  "equipe:pausar": pausarEquipe,
  "equipe:ativar": ativarEquipe,
  "colaborador:novo": novoColaborador
}