import React, { useState } from 'react'
import './Detail.css'
import { Tabs, Rate, Tag, Button, Form, Input, Card, Avatar, Popover, List, Pagination } from 'antd';
import moment from 'moment/moment';
import { useDispatch, useSelector } from 'react-redux';
import { useEffect } from 'react';
import { NavLink, useHistory } from 'react-router-dom';
import { capNhatBinhLuanPhimAction, layChiTietBinhLuanPhimAction, layDanhSachBinhLuanPhimAction, layThongTinPhimAction, themBinhLuanPhimAction, xoaBinhLuanPhimAction } from '../../redux/actions/QuanLyPhimAction';
import { layLichChieuTheoPhimAction } from '../../redux/actions/QuanLyDatVeAction';
import dayjs from 'dayjs'
import _ from 'lodash';
import { GET_BINH_LUAN_DETAIL_PHIM } from '../../redux/constants';
import { useFormik } from 'formik';
import { TOKEN } from '../../util/settings/config';
import { layDanhSachNguoiDungAction } from '../../redux/actions/QuanLyNguoiDungAction';
import { quanLyPhimService } from '../../services/QuanLyPhimService';
const { TabPane } = Tabs;

export default function Detail(props) {
    const { TextArea } = Input;
    const { movieEditDetail } = useSelector(state => state.MovieReducer);
    // const { lichChieuTheoPhim } = useSelector(state => state.QuanLyDatVeReducer);
    const { userLogin } = useSelector(state => state.UserReducer)
    // const { arrUser } = useSelector(state => state.UserReducer)
    const { arrBinhLuanPhim } = useSelector(state => state.MovieReducer);
    const { detailBinhLuanPhim } = useSelector(state => state.MovieReducer);
    const dispatch = useDispatch();
    let { id } = props.match.params;
    useEffect(() => {
        dispatch(layThongTinPhimAction(id))
        dispatch(layLichChieuTheoPhimAction(id))
        dispatch(layDanhSachBinhLuanPhimAction(id))
        dispatch(layDanhSachNguoiDungAction())
    }, [])

    // State cho lịch chiếu theo city
    const [lichChieuCity, setLichChieuCity] = useState({ raps: {}, phim: {} });
    const [selectedTinh, setSelectedTinh] = useState(null);
    const [selectedRap, setSelectedRap] = useState(null);
    useEffect(() => {
        async function fetchLichChieuCity() {
            try {
                const res = await quanLyPhimService.layLichChieuPhimTheoCity(id);
                if (res.data.status === 200) {
                    setLichChieuCity(res.data);
                }
            } catch (e) {
                setLichChieuCity({ raps: {}, phim: {} });
            }
        }
        fetchLichChieuCity();
    }, [id]);

    //Phan trang
    const [currentPage, setCurrentPage] = useState(1);
    const [postsPerPage] = useState(5);
    // const indexOfLastPost = currentPage * postsPerPage;
    // const indexOfFirstPost = indexOfLastPost - postsPerPage;
    // const reveseArrBinhLuanPhim = arrBinhLuanPhim.slice().reverse()
    // const currentArrBinhLuan = reveseArrBinhLuanPhim.slice(indexOfFirstPost, indexOfLastPost);


    const [form] = Form.useForm();
    const formik = useFormik({
        enableReinitialize: true,
        initialValues: {
            maPhim: id,
            username: userLogin.name,
            useremail: userLogin.email,
            comment: detailBinhLuanPhim?.comment,
        },
        onSubmit: (values) => {
            let formData = new FormData();
            for (let key in values) {
                formData.append(key, values[key]);
            }
            console.table('formData123', [...formData])
            if (!detailBinhLuanPhim.maComment) {
                dispatch(themBinhLuanPhimAction(id, formData))
            } else {
                dispatch(capNhatBinhLuanPhimAction(detailBinhLuanPhim.maComment, formData))
                dispatch(layDanhSachBinhLuanPhimAction(id))
                dispatch({
                    type: GET_BINH_LUAN_DETAIL_PHIM,
                    detailBinhLuanPhim: {}
                })
            }
            values.comment = '';
        }
    })

    // State cho reply
    const [replyTo, setReplyTo] = useState(null);
    const [replyForm] = Form.useForm();

    // Hàm gửi reply
    const handleReply = (parentId, values) => {
        const formData = new FormData();
        formData.append('comment', values.replyComment);
        formData.append('maPhim', id);
        formData.append('parent_id', parentId);
        dispatch(themBinhLuanPhimAction(id, formData));
        setReplyTo(null);
        replyForm.resetFields();
    };

    // Render replies lồng
    const renderReplies = (replies) => {
        if (!replies || !Array.isArray(replies) || replies.length === 0) return null;
        return replies.map((reply, idx) => (
            <Card key={idx} className="ml-10 my-2 bg-gray-50">
                <div className='d-flex align-center'>
                    {reply.userAvatar
                        ? <div style={{ width: 32, height: 32, minWidth: '32px', minHeight: 32, backgroundSize: 'cover', borderRadius: '50%', backgroundImage: `url(${reply.userAvatar})` }} />
                        : <Avatar size={32} style={{ fontSize: '18px', lineHeight: '24px' }} icon={reply.userName ? reply.userName.substr(0, 1) : '?'} />}
                    <div className='w-full'>
                        <p className='m-2 my-auto text-danger'>{reply.userName || 'Ẩn danh'}</p>
                        <p className='my-auto ml-2 text-xs'>{dayjs(reply.time || reply.created_at).format('DD-MM-YYYY')}</p>
                    </div>
                </div>
                <div className='mt-2 text-slate-700'> {reply.comment} </div>
                {/* Có thể cho phép reply tiếp ở đây nếu muốn lồng nhiều cấp */}
            </Card>
        ));
    };

    // Group bình luận cha/con
    const groupComments = (comments) => {
        const map = {};
        const roots = [];
        comments.forEach(c => {
            c.replies = [];
            map[c.maComment] = c;
        });
        comments.forEach(c => {
            if (c.parent_id) {
                if (map[c.parent_id]) {
                    map[c.parent_id].replies.push(c);
                }
            } else {
                roots.push(c);
            }
        });
        return roots;
    };
    const binhLuanGoc = groupComments(arrBinhLuanPhim);

    // Sửa lại renderBinhLuanPhim để hiển thị reply và form reply
    const renderBinhLuanPhim = () => {
        return binhLuanGoc.map((item, index) => {
            const content = (
                <div className='flex-col d-flex'>
                    <Button className='btn' type='link' onClick={() => {
                        dispatch(layChiTietBinhLuanPhimAction(item.maComment))
                    }}>Sửa</Button>
                    <Button className='btn' danger type='link' onClick={() => {
                        if (window.confirm('Bạn có chắc chắn muốn xóa bình luận này?')) {
                            dispatch(xoaBinhLuanPhimAction(item.maComment))
                            dispatch(layDanhSachBinhLuanPhimAction(id))
                        }
                    }}>Xóa</Button>
                </div>
            );
            return <Card
                key={index}
                className='w-full my-3 no-underline d-flex'
                style={{ minHeight: 130, overflow: 'hidden' }}
                bodyStyle={{ width: '100%' }}
            >
                <div className='d-flex align-center'>
                    {item.userAvatar
                        ? <div style={{ width: 40, height: 40, minWidth: '40px', minHeight: 40, backgroundSize: 'cover', borderRadius: '50%', backgroundImage: `url(${item.userAvatar})` }} />
                        : <Avatar size={40} style={{ fontSize: '28px', lineHeight: '32px' }} icon={item.userName ? item.userName.substr(0, 1) : '?'} />}
                    <div className='w-full'>
                        <p className='m-3 my-auto text-danger'>{item.userName || 'Ẩn danh'}</p>
                        <p className='my-auto ml-3'>{dayjs(item.time || item.created_at).format('DD-MM-YYYY')}</p>
                    </div>
                    {item.userId === userLogin.id || userLogin.role === 'QuanTri' || userLogin.role === 'Super' ? <Popover placement="bottomRight" content={content} trigger="hover">
                        <div className='px-3 border-none cursor-pointer btn drop-shadow-none hover:bg-gray-100'>...</div>
                    </Popover> : ''}
                </div>
                <div className='mt-3 text-slate-700'> {item.comment} </div>
                <div className='mt-2'>
                    <Button size="small" type="link" onClick={() => setReplyTo(item.maComment)}>
                        Trả lời
                    </Button>
                    {replyTo === item.maComment && (
                        <Form form={replyForm} layout="inline" onFinish={(values) => handleReply(item.maComment, values)} className="mt-2">
                            <Form.Item name="replyComment" rules={[{ required: true, message: 'Nhập nội dung trả lời!' }]}>
                                <Input placeholder="Nhập trả lời..." />
                            </Form.Item>
                            <Form.Item>
                                <Button htmlType="submit" type="primary" size="small">Gửi</Button>
                            </Form.Item>
                        </Form>
                    )}
                </div>
                {/* Hiển thị replies lồng */}
                {renderReplies(item.replies)}
            </Card>
        })
    };

    const history = useHistory();

    return (
        <div style={{ position: 'absolute', height: 'auto', width: '100%' }}>
            <div style={{
                backgroundImage: `url(${movieEditDetail.hinhAnh})`,
                height: 'auto',
                width: '100%',
                filter: 'blur(15px)',
                zIndex: -10,
                position: 'absolute',
                top: 0,
                left: 0,
                right: 0,
                bottom: 0,
                backgroundSize: 'cover',
                backgroundRepeat: 'no-repeat',
                backgroundPosition: 'center',
            }}
            ></div>
            <div style={{
                backgroundImage: `url(${movieEditDetail.hinhAnh})`,
                height: 'auto',
                width: '100%',
                zIndex: -20,
                position: 'absolute',
                top: 0,
                left: 0,
                right: 0,
                bottom: 0,
                backgroundSize: 'cover',
                backgroundRepeat: 'no-repeat',
                backgroundPosition: 'center',
            }}
            ></div>
            <div className='relative z-10 h-full row mt-60'>
                <div className='items-center justify-end h-full col-4 d-flex' >
                    <img className='object-cover mr-3 posterphim' src={movieEditDetail.hinhAnh} alt={movieEditDetail.hinhAnh} />
                </div>
                <div className='z-10 items-center justify-start col-4 d-flex'>
                    <div className='text-white'>
                        <p>Ngày chiếu: {moment(movieEditDetail.ngayKhoiChieu).format('DD.MM.YYYY')}</p>
                        <p className='text-4xl'>{movieEditDetail.tenPhim}</p>
                        <Rate allowHalf value={movieEditDetail.danhGia / 2} />
                    </div>
                </div>
                <div className='items-center justify-start col-4 d-flex' >
                    <div className={`c100 p${movieEditDetail.danhGia / 10 * 100} big green`}>
                        <span>{movieEditDetail.danhGia}/10</span>
                        <div className="slice">
                            <div className="bar" />
                            <div className="fill" />
                        </div>
                    </div>

                </div>
            </div>
            <div className='container px-5 pb-2 mb-5 rounded-2xl' style={{ minHeight: '500px', background: 'rgba(204, 204, 204, 0.5)' }}>

                <Tabs defaultActiveKey='1' centered className='mt-20 text-white'>
                    <TabPane tab={<p className='px-5 py-2 text-lg rounded-full bg-slate-800'>Lịch Chiếu</p>} key="1" >
                        {/* Chọn tỉnh thành */}
                        <div className='mb-4'>
                            {Object.keys(lichChieuCity.raps || {}).map((tinh, idx) => (
                                <button
                                    key={idx}
                                    className={`text-white mr-3 mt-3 bg-purple-700 hover:bg-purple-800 focus:outline-none focus:ring-4 focus:ring-purple-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mb-2 ${selectedTinh === tinh ? 'ring-4 ring-purple-400' : ''}`}
                                    onClick={() => { setSelectedTinh(tinh); setSelectedRap(null); }}
                                >
                                    {tinh}
                                </button>
                            ))}
                        </div>
                        {/* Chọn rạp */}
                        {selectedTinh && (
                            <div className='mb-4'>
                                {Object.keys(lichChieuCity.raps[selectedTinh] || {}).map((rap, idx) => (
                                    <button
                                        key={idx}
                                        className={`text-white mr-3 mt-3 bg-indigo-700 hover:bg-indigo-800 focus:outline-none focus:ring-4 focus:ring-indigo-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mb-2 ${selectedRap === rap ? 'ring-4 ring-indigo-400' : ''}`}
                                        onClick={() => setSelectedRap(rap)}
                                    >
                                        {rap}
                                    </button>
                                ))}
                            </div>
                        )}
                        {/* Hiển thị suất chiếu */}
                        {selectedTinh && selectedRap && (
                            <div className='mb-4'>
                                <h4 className='mb-2 font-bold'>Suất chiếu:</h4>
                                {lichChieuCity.raps[selectedTinh][selectedRap].length === 0 && <p>Không có suất chiếu.</p>}
                                {lichChieuCity.raps[selectedTinh][selectedRap].map((suat, idx) => (
                                    <NavLink key={idx} to={`/checkout/${suat.maLichChieu}`}>
                                        <Tag className='px-3 mr-3 text-lg' color='green'>
                                            {dayjs(suat.ngayChieu).format('DD-MM-YYYY')} {suat.gioChieu}
                                        </Tag>
                                    </NavLink>
                                ))}
                            </div>
                        )}

                    </TabPane>
                    <TabPane tab={<p className='px-5 py-2 text-lg rounded-full bg-slate-800'>Thông Tin</p>} key="2">
                        <p className='text-lg text-slate-900'>{movieEditDetail.moTa}</p>
                    </TabPane>
                    <TabPane tab={<p className='px-5 py-2 text-lg rounded-full bg-slate-900'>Đánh Giá</p>} key="3">
                        <h1 className='mt-5 text-xl text-slate-900'>Bình Luận Từ Người Xem</h1>
                        <div className='p-2 mb-5 bg-light rounded-xl '>

                            {(localStorage.getItem(TOKEN)) ? <Form form={form} onSubmitCapture={formik.handleSubmit} className='flex-col items-end w-full d-flex' >
                                <Form.Item label="" className='w-full mb-2' >
                                    <TextArea name='comment' allowClear rows={4} placeholder='nhập bình luận' onChange={formik.handleChange} value={formik.values.comment} />
                                </Form.Item>
                                <button disabled={!formik.values.comment?.trim()} type="submit" className="p-2 px-5 text-white bg-blue-700 rounded-full disabled:opacity-25">Gửi</button>
                            </Form> : <Button href="/login" className='w-full'>Vui lòng đăng nhập để bình luận</Button>}

                        </div>
                        {renderBinhLuanPhim()}
                        <Pagination className='justify-center mb-20 d-flex line-clamp-3' pageSize={postsPerPage} currentPage={currentPage} total={arrBinhLuanPhim.length} onChange={(page) => { setCurrentPage(page) }} />
                    </TabPane>
                </Tabs>
                <button
                    onClick={() => history.push('/chat')}
                    className="btn btn-primary"
                    style={{ margin: '16px 0' }}
                >
                    Chat với quản trị
                </button>
            </div>
        </div >

    )
}
