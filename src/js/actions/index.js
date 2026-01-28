import products from "./products/index.js";
import offers from "./offers/index.js";
import floatingActions from "./floating-actions/index.js";
import form from "./form/index.js";
import core from "./core/index.js";
import ui from "./ui/index.js";
import teamusers from "./teamusers/index.js";

export const Actions = {
  ...core,
  ...ui,
  ...teamusers,
  ...products,
  ...offers,
  ...floatingActions,
  ...form
};
