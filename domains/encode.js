import fs from "fs";

const imagePath = "D:/jasmine/image2.jpg";   // <-- ใส่ path จริงบนเครื่องคุณ

const image = fs.readFileSync(imagePath, { encoding: "base64" });

console.log(image);
