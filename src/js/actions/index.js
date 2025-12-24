import usuarios from "./usuarios/index.js";
import equipes from "./equipes/index.js";

export const Actions = {
  ...usuarios,
  ...equipes
};
