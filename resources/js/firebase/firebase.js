import { initializeApp } from "firebase/app";
import { getStorage, ref, uploadBytes, deleteObject, getDownloadURL } from "firebase/storage";

// Khởi tạo Firebase
const firebaseConfig = {
  apiKey: import.meta.env.VITE_FIREBASE_API_KEY,
  authDomain: import.meta.env.VITE_FIREBASE_AUTH_DOMAIN,
  projectId: import.meta.env.VITE_FIREBASE_PROJECT_ID,
  storageBucket: import.meta.env.VITE_FIREBASE_STORAGE_BUCKET,
  messagingSenderId: import.meta.env.VITE_FIREBASE_MESSAGING_SENDER_ID,
  appId: import.meta.env.VITE_FIREBASE_APP_ID,
  measurementId: import.meta.env.VITE_FIREBASE_MEASUREMENT_ID,
};

const app = initializeApp(firebaseConfig);
const storage = getStorage(app);

// Hàm upload ảnh lên Firebase Storage
export async function uploadImage(file, path = "images/") {
  try {
    const storageRef = ref(storage, `${path}/${file.name}`);
    const snapshot = await uploadBytes(storageRef, file);
    const downloadURL = await getDownloadURL(snapshot.ref);
    return downloadURL;  // Trả về URL của ảnh sau khi upload
  } catch (error) {
    console.error("Lỗi khi upload ảnh:", error);
    throw error;
  }
}

// Hàm lấy URL của ảnh từ Firebase Storage
export async function getImageURL(filePath) {
  try {
    const fileRef = ref(storage, filePath);
    const url = await getDownloadURL(fileRef);
    return url; // Trả về URL của ảnh
  } catch (error) {
    console.error("Lỗi khi lấy URL ảnh:", error);
    throw error;
  }
}

// Hàm xóa ảnh trên Firebase Storage
export async function deleteImage(filePath) {
  try {
    const fileRef = ref(storage, filePath);
    await deleteObject(fileRef);
    console.log(`Đã xóa ảnh tại đường dẫn: ${filePath}`);
  } catch (error) {
    console.error("Lỗi khi xóa ảnh:", error);
    throw error;
  }
}

export default {
  uploadImage,
  getImageURL,
  deleteImage,
};
