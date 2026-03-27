import mongoose, { Document, Schema } from "mongoose";
import Roles from "../utils/Role.ts";

interface EmployeeT extends Document {
  name: String;
  role: "Admin" | "Owner" | "Barista" | "Kasir";
  password: String;
  email: String;
  photo: String;
}

const employeeSchema = new Schema<EmployeeT>(
  {
    name: { type: String, required: true },
    password: { type: String, required: true },
    email: { type: String, required: true, unique: true },
    role: {
      type: String,
      required: true,
      enum: [Roles.Admin, Roles.Kasir, Roles.Barista, Roles.Barista],
    },
    photo: {
      type: String,
    },
  },
  { timestamps: true },
);

const Employee = mongoose.model<EmployeeT>("Employee", employeeSchema);

export default Employee;
