import type { Request, Response } from "express";
import response from "../utils/response.ts";
import cloudinary from "../utils/cloudinary.ts";
import Product from "../models/product.model.ts";

export const createProduct = async (req: Request, res: Response) => {
  try {
    const { name, price, stock, isAvailable } = req.body;
    let photoUrl = null;

    if (req.file) {
      const result = await cloudinary.uploader.upload(req.file.path);
      photoUrl = result.secure_url;
    }

    const product = await Product.create({
      name,
      price: Number(price),
      stock: Number(stock),
      isAvailable: Boolean(isAvailable),
      photo: photoUrl,
    });

    response.successCreate(res, "berhasil buat product", 201, { product });
  } catch (error) {
    response.serverError(res, "gagal pas buat product");
  }
};

export const getAllProduct = async (req: Request, res: Response) => {
  try {
    const products = await Product.find();
    response.successCreate(res, "berhasil dapetin semua product", 200, {
      products,
    });
  } catch (error) {
    return response.serverError(res, "gagal pas getAllProduct");
  }
};
