import type { Response } from "express";

const response = {
  serverError: (res: Response, message: String) => {
    return res.status(500).json({ status: "failed", message, data: null });
  },
  clientError: (res: Response, message: String, code: number = 400) => {
    return res.status(code).json({ status: "failed", message, data: null });
  },
  notFound: (res: Response, message: string) => {
    return res.status(404).json({ status: "failed", message, data: null });
  },
  unauthorized: (res: Response, message: string) => {
    return res.status(401).json({ status: "failed", message, data: null });
  },
  forbidden: (res: Response, message: string) => {
    return res.status(403).json({ status: "failed", message, data: null });
  },
  successCreate: (
    res: Response,
    message: string,
    code: number = 201,
    data: any,
  ) => {
    return res.status(code).json({ status: "succes", message, data });
  },
};

export default response;
