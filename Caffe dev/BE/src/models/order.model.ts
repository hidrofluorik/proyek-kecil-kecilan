import mongoose, { Document, Schema } from "mongoose";

interface OrderT extends Document {
  orderBy: mongoose.Types.ObjectId; // Gunakan tipe ObjectId agar sinkron dengan Schema
  total: number;
  status: "Done" | "Pending" | "Cancelled";
  items: { productId: mongoose.Types.ObjectId; quantity: number; price: number }[];
}

const orderSchema = new Schema<OrderT>(
  {
    // Pastikan ref: "Employee" sesuai dengan nama model di file employee.model.ts
    orderBy: { type: Schema.Types.ObjectId, ref: "Employee", required: true },
    total: { type: Number, required: true },
    status: {
      type: String,
      enum: ["Done", "Pending", "Cancelled"],
      default: "Done",
    },
    // Perbaikan struktur array of objects untuk items
    items: [
      {
        productId: { type: Schema.Types.ObjectId, ref: "Product", required: true },
        quantity: { type: Number, required: true },
        price: { type: Number, required: true },
      },
    ],
  },
  { timestamps: true },
);

const Order = mongoose.model<OrderT>("Order", orderSchema);

export default Order;