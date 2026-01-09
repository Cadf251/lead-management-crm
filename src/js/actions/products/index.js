import { createProduct } from "./create-product";
import { deleteProduct } from "./delete-product";
import { editProduct } from "./edit-product";

export default {
  "product:create": createProduct,
  "product:edit": editProduct,
  "product:delete": deleteProduct
}