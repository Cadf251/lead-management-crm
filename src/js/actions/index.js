import usuarios from "./usuarios/index.js";
import equipes from "./equipes/index.js";
import products from "./products/index.js";

export const Actions = {
  ...usuarios,
  ...equipes,
  ...products
};
