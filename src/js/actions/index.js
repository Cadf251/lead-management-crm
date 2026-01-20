import equipes from "./equipes/index.js";
import products from "./products/index.js";
import offers from "./offers/index.js";
import floatingActions from "./floating-actions/index.js";
import form from "./form/index.js";
import core from "./core/index.js";
import ui from "./ui/index.js";

export const Actions = {
  ...core,
  ...ui,
  ...equipes,
  ...products,
  ...offers,
  ...floatingActions,
  ...form
};
