import type { NextFunction, Request, Response } from "express";
import response from "../utils/response.ts";

const isUserAuthorized =
  (ROLES: string[]) => (req: Request, res: Response, next: NextFunction) => {
    const role = req.employee.role;
    // console.log(role);
    if (!role || !ROLES.includes(role)) {
      return response.unauthorized(res, "ga boleh lu gabisa anj");
    }
    next();
  };

export default isUserAuthorized;
