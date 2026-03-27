import mongoose, { Document, Schema } from "mongoose";
import Roles from "../utils/Role.ts";

interface ProductT extends Document {
  name: string;
  price: number;
  isAvailable: boolean;
  stock: number;
  photo: string;
}

const productSchema = new Schema<ProductT>(
  {
    name: { type: String, required: true },
    price: { type: Number, required: true },
    photo: { type: String, required: true },
    stock: { type: Number, required: true },
    isAvailable: { type: Boolean, required: true },
  },
  { timestamps: true },
);

const Product = mongoose.model<ProductT>("Product", productSchema);

export default Product;
