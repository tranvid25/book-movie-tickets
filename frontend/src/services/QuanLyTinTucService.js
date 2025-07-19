/* eslint-disable no-useless-constructor */
import { baseService } from "./baseService";

export class QuanLyTinTucService extends baseService {
    constructor() {
        super();
    }

    layDanhSachTinTuc = (id = '') => {
        if (id != '') {
            return this.get(`/api/laydanhsachtintuc/${id}`);

        } else {
            return this.get(`/api/laydanhsachtintuc`);

        }
    }

    themTinTuc = (formData) => {
        return this.post(`/api/laydanhsachtintuc`, formData);
    }

    capNhatTinTuc = (id,formData) => {
        return this.post(`/api/auth/laydanhsachtintuc/${id}/update`,formData);
    }

    xoaTinTuc = (id) => {
        return this.delete(`/api/auth/laydanhsachtintuc/${id}/delete`);
    }

    layBinhLuanBaiViet = (id) => {
        return this.get(`/api/auth/laydanhsachbinhluan/${id}`);
    }

    themBinhLuanBaiViet = (binhLuan) => {
        return this.post(`/api/auth/laydanhsachbinhluan`,binhLuan);
    }

    xoaBinhLuan = (id) => {
        return this.delete(`/api/auth/laydanhsachbinhluan/${id}/delete`);
    }


    capNhatBinhLuan = (id,formData) => {
        return this.post(`/api/auth/laydanhsachbinhluan/${id}/update`,formData);
    }

    layBinhLuan = (id) => {
        return this.get(`/api/auth/laydanhsachbinhluan/${id}/edit`);
    }


    timkiem = (content) => {
        return this.get(`/api/timkiem/search/?search=${content}`)
    }

}

export const quanLyTinTucService = new QuanLyTinTucService();