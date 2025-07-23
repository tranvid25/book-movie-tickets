import { TOKEN, USER_LOGIN } from "../../util/settings/config";
import { DANG_NHAP_ACTION, LAY_CHI_TIET_NGUOI_DUNG, LAY_DANH_SACH_NGUOI_DUNG, SET_THONG_TIN_DAT_VE, TIM_KIEM_NGUOI_DUNG } from "../constants"

let user = {}
try {
  const userStr = localStorage.getItem(USER_LOGIN)
  if (userStr && userStr !== "undefined") {
    user = JSON.parse(userStr)
}
} catch (e) {
  user = {}
}


const initialState = {
  userLogin: user,
  thongTinNguoiDung: {},
  chiTietNguoiDung: {},
  arrUser: [
    // {
    //   "taiKhoan": "01Admin123",
    //   "hoTen": "Tung Son",
    //   "email": "thong000@gmail.com",
    //   "soDt": "0987654321",
    //   "matKhau": "iphone12",
    //   "maLoaiNguoiDung": "QuanTri"
    // }
  ],
  profile: {}
}


export const UserReducer = (state = initialState, action) => {
  switch (action.type) {

    case DANG_NHAP_ACTION:
      const { thongTinDangNhap } = action;
      const userContent = thongTinDangNhap?.user;
      localStorage.setItem(USER_LOGIN, JSON.stringify(userContent || {}));
      localStorage.setItem(TOKEN, thongTinDangNhap?.accessToken || '');
      if (userContent && userContent.id) {
        localStorage.setItem('userId', userContent.id);
      }
      return { ...state, userLogin: userContent || {} }

    case LAY_DANH_SACH_NGUOI_DUNG:
      state.arrUser = action.arrUser;
      // state.profile = state.arrUser.find(item => item.email === state.userLogin.email)
      return { ...state }

    case LAY_CHI_TIET_NGUOI_DUNG:
      state.profile = action.profile;

      return { ...state }

    case SET_THONG_TIN_DAT_VE:
      state.thongTinNguoiDung = action.thongTinNguoiDung;
      return { ...state }
    default:
      return state
  }
}
