import express from "express";
import ENV from "./utils/ENV.ts";
import connect from "./services/Mongo.ts";
import employeeRouter from "./routes/employee.route.ts";
import authRouter from "./routes/auth.route.ts";
import productRouter from "./routes/products.route.ts";
import { setupSwagger } from "./utils/swagger.ts";
import orderRouter from "./routes/order.route.ts";
import cors from 'cors';

const PORT = ENV.PORT;
const app = express();

connect();
// Pasang CORS di sini, sebelum route yang lain!
app.use(cors()); 
app.use(express.json());

app.use(express.json());
app.use("/employees", employeeRouter);
app.use("/auth", authRouter);
app.use("/products", productRouter);
app.use("/orders", orderRouter);

setupSwagger(app);

app.listen(PORT, () => {
  console.log(`jalan di port ${PORT}`);
});
