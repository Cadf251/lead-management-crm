import usuarios from "./usuarios/index.js";
import equipes from "./equipes/index.js";
import products from "./products/index.js";
import offers from "./offers/index.js";
import floatingActions from "./floating-actions/index.js";

export const Actions = {
  ...usuarios,
  ...equipes,
  ...products,
  ...offers,
  ...floatingActions
};
