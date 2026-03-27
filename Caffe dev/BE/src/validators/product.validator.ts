import { z } from "zod";

export const productSchema = z.object({
  name: z.string(),
  price: z.coerce.number(),
  isAvailable: z.coerce.boolean(),
  stock: z.coerce.number(),
});
