import jwt from "jsonwebtoken";
import response from "../utils/response.ts";
import type { NextFunction, Request, Response } from "express";
import ENV from "../utils/ENV.ts";

const verifyToken = (req: Request, res: Response, next: NextFunction) => {
  const tokenHeader = req.headers.authorization;

  if (!tokenHeader) {
    return response.unauthorized(res, "token ga ada");
  }

  const [bearer, token] = tokenHeader.split(" ");

  if (bearer !== "Bearer" || !token) {
    return response.unauthorized(res, "format token salah");
  }

  jwt.verify(token, ENV.JWT_SECRET, (err, decoded) => {
    if (err) {
      return response.forbidden(res, "token ga valid");
    }

    req.employee = {
      userId: decoded.employeeId,
      role: decoded.employeeRole,
    };
    next();
  });
};

export default verifyToken;
