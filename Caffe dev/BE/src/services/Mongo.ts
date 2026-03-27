import mongoose from "mongoose";
import ENV from "../utils/ENV.ts";

const connect = async () => {
  try {
    await mongoose.connect("mongodb://127.0.0.1:27017/caffe_db");
    console.log("berhasil konek db");
  } catch (error) {
    console.log(error);
    //process.exit(1);
  }
};

export default connect;
