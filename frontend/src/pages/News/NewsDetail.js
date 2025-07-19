import React, { useEffect, useState } from "react";
import { useDispatch, useSelector } from "react-redux";
import {
  capNhatBinhLuanAction,
  layChiTietBinhLuanAction,
  layDanhSachBinhLuanAction,
  layDanhSachTinTucAction,
  themBinhLuanAction,
  xoaBinhLuanAction,
} from "../../redux/actions/QuanLyTinTucAction";
import {
  Card,
  Avatar,
  Form,
  Input,
  Popover,
  Button,
  List,
  Pagination,
} from "antd";
import { useFormik } from "formik";
import dayjs from "dayjs";
import { GET_BINH_LUAN_DETAIL } from "../../redux/constants";
import { TOKEN } from "../../util/settings/config";
import { layDanhSachNguoiDungAction } from "../../redux/actions/QuanLyNguoiDungAction";

export default function NewsDetail(props) {
  const { TextArea } = Input;
  const { arrBinhLuan, arrTinTuc, detailTinTuc, detailBinhLuan } = useSelector(
    (state) => state.NewsReducer
  );
  const { userLogin, arrUser } = useSelector((state) => state.UserReducer);

  const dispatch = useDispatch();

  let { id } = props.match.params;
  useEffect(() => {
    dispatch(layDanhSachTinTucAction(id));
    dispatch(layDanhSachBinhLuanAction(id));
    dispatch(layDanhSachTinTucAction());
    dispatch(layDanhSachNguoiDungAction());
  }, []);

  const [form] = Form.useForm();
  const formik = useFormik({
    enableReinitialize: true,
    initialValues: {
      maBaiViet: id,
      username: userLogin.name,
      useremail: userLogin.email,
      comment: detailBinhLuan?.comment,
    },
    onSubmit: (values) => {
      let formData = new FormData();
      for (let key in values) {
        formData.append(key, values[key]);
      }
      console.table("formData123", [...formData]);
      if (!detailBinhLuan.maComment && !!values.comment !== "") {
        dispatch(themBinhLuanAction(id, formData));
      } else {
        dispatch(capNhatBinhLuanAction(detailBinhLuan.maComment, formData));
        dispatch(layDanhSachBinhLuanAction(id));
        dispatch({
          type: GET_BINH_LUAN_DETAIL,
          detailBinhLuan: {},
        });
      }
      values.comment = "";
    },
  });

  //Phan trang
  const [currentPage, setCurrentPage] = useState(1);
  const [postsPerPage] = useState(5);
  const indexOfLastPost = currentPage * postsPerPage;
  const indexOfFirstPost = indexOfLastPost - postsPerPage;
  const reveseArrBinhLuan = arrBinhLuan.slice().reverse();
  const currentArrBinhLuan = reveseArrBinhLuan.slice(
    indexOfFirstPost,
    indexOfLastPost
  );

  const renderBinhLuan = () => {
    return currentArrBinhLuan.map((item, index) => {
      const content = (
        <div className="flex-col d-flex">
          <Button
            className="btn"
            type="link"
            onClick={() => {
              dispatch(layChiTietBinhLuanAction(item.maComment));
            }}
          >
            Sửa
          </Button>
          <Button
            className="btn"
            danger
            type="link"
            onClick={() => {
              if (window.confirm("Bạn có chắc chắn muốn xóa bình luận này?")) {
                dispatch(xoaBinhLuanAction(item.maComment));
                dispatch(layDanhSachBinhLuanAction(id));
              }
            }}
          >
            Xóa
          </Button>
        </div>
      );
      return (
        <Card
          className="w-full my-3 no-underline d-flex"
          style={{ minHeight: 130, overflow: "hidden" }}
          bodyStyle={{ width: "100%" }}
        >
          <div className="d-flex align-center">
            {item.userAvatar ? (
              <div
                style={{
                  width: 40,
                  height: 40,
                  minWidth: "40px",
                  minHeight: 40,
                  backgroundSize: "cover",
                  borderRadius: "50%",
                  backgroundImage: `url(${item.userAvatar})`,
                }}
              />
            ) : (
              <Avatar
                size={40}
                style={{ fontSize: "28px", lineHeight: "32px" }}
                icon={item.userName ? item.userName.substr(0, 1) : "?"}
              />
            )}
            <div className="w-full">
              <p className="m-3 my-auto text-danger">
                {item.userName || "Ẩn danh"}
              </p>
              <p className="my-auto ml-3">
                {dayjs(item.time || item.created_at).format("DD-MM-YYYY")}
              </p>
            </div>
            {item.useremail === userLogin.email ||
            userLogin.role === "QuanTri" ||
            userLogin.role === "Super" ? (
              <Popover
                placement="bottomRight"
                content={content}
                trigger="hover"
              >
                <div className="px-3 border-none cursor-pointer btn drop-shadow-none hover:bg-gray-100">
                  ...
                </div>
              </Popover>
            ) : (
              ""
            )}
          </div>

          <div className="mt-3 text-slate-700"> {item.comment} </div>
        </Card>
      );
    });
  };

  return (
    <div>
      <div className="container">
        <div
          className="absolute z-10 items-center d-flex"
          style={{ top: "28%" }}
        >
          <div className="container">
            <h2 className="text-5xl text-white drop-shadow-md">
              {detailTinTuc.tieuDe}
            </h2>
            <div className="text-white end__text drop-shadow-md">
              {detailTinTuc.noiDungPhu}
            </div>
          </div>
        </div>
      </div>

      <div
        className=""
        style={{
          backgroundImage: `url(${detailTinTuc.hinhAnh})`,
          height: 700,
          backgroundSize: "cover",
          filter: "brightness(0.5)",
        }}
      ></div>
      <div
        className="container relative z-10 p-10 mb-10 bg-white rounded-lg shadow-lg"
        style={{ marginTop: "-120px" }}
      >
        <div className="row">
          <div className="col-8">
            <div className="mb-5">
              <div className="justify-between p-3 d-flex">
                <div className="d-flex">
                  <p className="p-2 border border-indigo-700 rounded-full text-danger">
                    {detailTinTuc.tacGia}
                  </p>
                  <p className="p-2 ml-3">{detailTinTuc.theLoai}</p>
                </div>

                <p className="p-2 text-gray-400">
                  {dayjs(detailTinTuc.created_at).format("DD-MM-YYYY")}
                </p>
              </div>
              <div
                dangerouslySetInnerHTML={{ __html: detailTinTuc.noiDung }}
              ></div>
            </div>
            <hr></hr>
            <h1 className="mt-5 text-xl">Bình Luận Từ Người Xem</h1>
            <div className="p-2 mb-5 bg-light rounded-xl ">
              {localStorage.getItem(TOKEN) ? (
                <Form
                  form={form}
                  onSubmitCapture={formik.handleSubmit}
                  className="flex-col items-end w-full d-flex"
                >
                  <Form.Item label="" className="w-full mb-2">
                    <TextArea
                      name="comment"
                      allowClear
                      rows={4}
                      placeholder="nhập bình luận"
                      onChange={formik.handleChange}
                      value={formik.values.comment}
                    />
                  </Form.Item>
                  <button
                    disabled={!formik.values.comment?.trim()}
                    type="submit"
                    className="p-2 px-5 text-white bg-blue-700 rounded-full disabled:opacity-25"
                  >
                    Gửi
                  </button>
                </Form>
              ) : (
                <Button href="/login" className="w-full">
                  Vui lòng đăng nhập để bình luận
                </Button>
              )}
            </div>
            {renderBinhLuan()}
            <Pagination
              className="justify-center mb-20 d-flex line-clamp-3"
              pageSize={postsPerPage}
              currentPage={currentPage}
              total={arrBinhLuan.length}
              onChange={(page) => {
                setCurrentPage(page);
              }}
            />
          </div>
          <div className="col-4">
            <div className="flex flex-col items-center w-full my-3 bg-white border border-gray-200 rounded-lg shadow-md md:flex-row">
              <div className="flex flex-col justify-between w-full p-4 leading-normal">
                <h5 className="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                  Bài Viết Mới Nhất
                </h5>
                <List
                  itemLayout="horizontal"
                  dataSource={arrTinTuc.slice(-5).reverse()}
                  renderItem={(item, index) => (
                    <List.Item>
                      <div className="w-full mt-1 mb-1 font-normal text-gray-700 d-flex dark:text-gray-400">
                        <img
                          className="rounded-md"
                          src={item.hinhAnh}
                          alt={item.hinhAnh}
                          style={{
                            width: 140,
                            height: 110,
                            objectFit: "cover",
                          }}
                        />
                        <div className="p-2">
                          <a
                            className="font-bold text-md"
                            href={`/news/detail/${item.maBaiViet}`}
                          >
                            {item.tieuDe}
                          </a>
                          <div className="overflow-hidden text-ellipsis line-clamp-2">
                            {item.noiDungPhu}
                          </div>
                        </div>
                      </div>
                    </List.Item>
                  )}
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
