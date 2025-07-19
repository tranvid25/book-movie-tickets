/* eslint-disable no-useless-constructor */
import { baseService } from "./baseService";

export class QuanLyPhimService extends baseService {
    constructor() {
        super();
    }

    layDanhSachPhim = (maPhim = '') => {
        if (maPhim != '') {
            return this.get(`/api/LayDanhSachPhim/${maPhim}`);

        } else {
            return this.get(`/api/LayDanhSachPhim`);

        }
    }

    themPhimUploadHinh = (formData) => {
        return this.post(`/api/LayDanhSachPhim`, formData);
    }

    layThongTinPhim = (maPhim) => {
        return this.get(`/api/LayDanhSachPhim/${maPhim}`);
    }

    capNhatPhimUpload = (maPhim, formData) => {
        return this.post(`/api/LayDanhSachPhim/${maPhim}/update`, formData);
    }

    xoaPhim = (maPhim) => {
        return this.delete(`/api/LayDanhSachPhim/${maPhim}/delete`);
    }


    //Review Phim
    layBinhLuanPhim = (id) => {
        if (id != '') {
            return this.get(`/api/auth/laydanhsachbinhluanphim/${id}`);
        } else {
            return this.get(`/api/laydanhsachbinhluanphim/`);
        }
    }

    themBinhLuanPhim = (binhLuan) => {
        return this.post(`/api/auth/laydanhsachbinhluanphim`, binhLuan);
    }

    xoaBinhLuanPhim = (id) => {
        return this.delete(`/api/laydanhsachbinhluanphim/${id}/delete`);
    }


    capNhatBinhLuanPhim = (id, formData) => {
        return this.post(`/api/laydanhsachbinhluanphim/${id}/update`, formData);
    }

    layChiTietBinhLuanPhim = (id) => {
        return this.get(`/api/auth/laydanhsachbinhluanphim/${id}/edit`);
    }

    layLichChieuPhimTheoCity = (maPhim) => {
        return this.get(`/api/LayDanhSachPhim/${maPhim}/City`);
    }

    searchPhim = (keyword) => {
        return this.get(`/api/movies/search?q=${encodeURIComponent(keyword)}`);
    }
}

export const quanLyPhimService = new QuanLyPhimService();