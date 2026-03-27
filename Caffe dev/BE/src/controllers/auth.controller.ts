import type { Request, Response } from "express";
import jwt from "jsonwebtoken";
import bcrypt from "bcrypt";
import response from "../utils/response.ts";
import Employee from "../models/employee.model.ts";
import ENV from "../utils/ENV.ts";

export const loginController = async (req: Request, res: Response) => {
  try {
    const { email, password } = req.body;
    const employee = await Employee.findOne({ email });
    if (!employee) {
      return response.notFound(res, "user gaketemu");
    }
    const isPasswordCorrect = await bcrypt.compare(password, employee.password);

    if (!isPasswordCorrect) {
      return response.clientError(res, "password salah");
    }
    const token = jwt.sign(
      { employeeId: employee._id, employeeRole: employee.role },
      ENV.JWT_SECRET,
      { expiresIn: ENV.JWT_EXPIRES },
    );

    response.successCreate(res, "berhasil bikin token", 200, { token });
  } catch (error) {
    response.serverError(res, "error di login");
  }
};

export const meController = async (req: Request, res: Response) => {
  try {
    const { userId } = req.employee;
    const employee = await Employee.findById(userId);
    if (!employee) {
      return response.notFound(res, "user gaketemu");
    }
    const { password: _, ...employeeData } = employee.toObject();
    response.successCreate(res, "berhasil dapet data user", 200, {
      employee: employeeData,
    });
  } catch (error) {
    response.serverError(res, "error dapetin data employee");
  }
};
