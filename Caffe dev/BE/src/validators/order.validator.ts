import { z } from "zod";

export const orderSchema = z.object({
  total: z.number(),
  items: z.array(z.object({ productId: z.string(), quantity: z.number() })),
  status: z.enum(["Done", "Pending", "Cancelled"]).optional(),
});
