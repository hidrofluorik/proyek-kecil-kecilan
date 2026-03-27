import { Router } from "express";
import { validate } from "../middlewares/reqBody.middleware.ts";
import isUserAuthorized from "../middlewares/rbac.middleware.ts";
import Roles from "../utils/Role.ts";
import { orderSchema } from "../validators/order.validator.ts";
import verifyToken from "../middlewares/acl.middleware.ts";
import { createOrder } from "../controllers/order.controller.ts";

const orderRouter = Router();

orderRouter.post(
  "/",
  [
    verifyToken,
    isUserAuthorized([Roles.Admin, Roles.Owner, Roles.Barista, Roles.Barista]),
    validate(orderSchema),
  ],
  createOrder,
);

export default orderRouter;
