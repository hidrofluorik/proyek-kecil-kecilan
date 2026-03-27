import { Router } from "express";
import verifyToken from "../middlewares/acl.middleware.ts";
import isUserAuthorized from "../middlewares/rbac.middleware.ts";
import Roles from "../utils/Role.ts";
import { validate } from "../middlewares/reqBody.middleware.ts";
import { productSchema } from "../validators/product.validator.ts";
import upload from "../middlewares/multer.middleware.ts";
import {
  createProduct,
  getAllProduct,
} from "../controllers/product.controller.ts";

const productRouter = Router();

/**
 * @swagger
 * /products:
 *   post:
 *     summary: Create a new product
 *     tags: [Products]
 *     security:
 *       - bearerAuth: []
 *     requestBody:
 *       required: true
 *       content:
 *         multipart/form-data:
 *           schema:
 *             type: object
 *             required:
 *               - name
 *               - price
 *               - isAvailable
 *               - stock
 *             properties:
 *               name:
 *                 type: string
 *               price:
 *                 type: number
 *               isAvailable:
 *                 type: boolean
 *               stock:
 *                 type: number
 *               photo:
 *                 type: string
 *                 format: binary
 *           example:
 *             name: "Kopi Susu Gula Aren"
 *             price: 25000
 *             isAvailable: true
 *             stock: 100
 *             photo: (binary file)
 *     responses:
 *       201:
 *         description: Product created successfully
 *         content:
 *           application/json:
 *             example:
 *               status: "succes"
 *               message: "berhasil buat product"
 *               data:
 *                 product:
 *                   _id: "65e6789abcd12345e6789f"
 *                   name: "Kopi Susu Gula Aren"
 *                   price: 25000
 *                   isAvailable: true
 *                   stock: 100
 *                   photo: "https://res.cloudinary.com/.../kopi.jpg"
 *                   createdAt: "2024-03-05T13:00:00.000Z"
 *                   updatedAt: "2024-03-05T13:00:00.000Z"
 *                   __v: 0
 *       400:
 *         description: Bad request
 *       401:
 *         description: Unauthorized
 *       403:
 *         description: Forbidden
 *
 *   get:
 *     summary: Get all products
 *     tags: [Products]
 *     security:
 *       - bearerAuth: []
 *     responses:
 *       200:
 *         description: List of products
 *         content:
 *           application/json:
 *             example:
 *               status: "succes"
 *               message: "berhasil dapetin semua product"
 *               data:
 *                 products:
 *                   - _id: "65e6789abcd12345e6789f"
 *                     name: "Kopi Susu Gula Aren"
 *                     price: 25000
 *                     isAvailable: true
 *                     stock: 100
 *                     photo: null
 *                     createdAt: "2024-03-05T13:00:00.000Z"
 *                     updatedAt: "2024-03-05T13:00:00.000Z"
 *                     __v: 0
 *       401:
 *         description: Unauthorized
 *       403:
 *         description: Forbidden
 */

productRouter.post(
  "/",
  [
    verifyToken,
    isUserAuthorized([Roles.Admin, Roles.Owner, Roles.Barista, Roles.Kasir]),
    upload.single("photo"),
    validate(productSchema),
  ],
  createProduct,
);

productRouter.get(
  "/",
  [
    verifyToken,
    isUserAuthorized([Roles.Admin, Roles.Owner, Roles.Barista, Roles.Kasir]),
  ],
  getAllProduct,
);

export default productRouter;
