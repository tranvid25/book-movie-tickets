import React, { Fragment } from 'react';
import { NavLink } from 'react-router-dom'
import { Button, Avatar, Popover, Input } from 'antd';
import { SearchOutlined } from '@ant-design/icons';
import _ from 'lodash';
import { TOKEN, USER_LOGIN } from '../../../util/settings/config';
import { history } from '../../../App';
import { layKetQuaTimKiem } from '../../../redux/actions/QuanLyTinTucAction';
import { useDispatch, useSelector } from 'react-redux';
import { quanLyPhimService } from '../../../services/QuanLyPhimService';
import { useHistory } from 'react-router-dom';
import { useState } from 'react';
import { debounce } from 'lodash';
import NotificationBell from '../../../components/Notification/NotificationBell';

export default function Header(props) {
    const dispatch = useDispatch()
    const { arrUser } = useSelector(state => state.UserReducer)
    let userLogin = {};
    try {
        const userStr = localStorage.getItem(USER_LOGIN);
        if (userStr && userStr !== "undefined") {
            userLogin = JSON.parse(userStr);
        }
    } catch (e) {
        userLogin = {};
    }

    let usLogin = arrUser?.find(obj => obj.id === userLogin.id)

    const content = (
        <div style={{ width: 200 }}>
            {(userLogin.role === 'Super') ? <Button type="text" className='w-full text-left' href="/admin/moviemng">Super Admin</Button> : ''}
            {(userLogin.role === 'QuanTri') ? <Button type="text" className='w-full text-left' href="/admin/moviemng">Trang Quản Trị</Button> : ''}
            <Button type="text" href="/users/profile" className='w-full text-left'>Trang Cá Nhân</Button>
            <Button type="text" href="/home" className='w-full text-left' onClick={() => {
                localStorage.removeItem(USER_LOGIN)
                localStorage.removeItem(TOKEN)
                window.location.reload()
            }}>Đăng Xuất</Button>
        </div>
    );

    const renderLogin = () => {
        if (_.isEmpty(userLogin)) {
            return <Fragment>
                <Button type="text" href="/register" className="text-white">Đăng Ký</Button>
                <Button type="primary" href="/login" className="font-semibold rounded-full bg-violet-500">Đăng Nhập</Button>
            </Fragment>
        } else {
            return <Popover placement="bottomRight" title={userLogin.name} content={content} trigger="click">
                <Button className='rounded-full bg-slate-300 p-0 d-flex justify-center items-center w-full h-full' style={{ width: 40, height: 40 }}>
                    {usLogin?.avatar == null || usLogin?.avatar == ""
                        ? <Avatar size={40} style={{ fontSize: '28px', lineHeight: '32px' }} icon={usLogin?.name.substr(0, 1)} />
                        : <div style={{ minWidth: '40px', minHeight: 40, backgroundSize: 'cover', borderRadius: '50%', backgroundImage: `url(${usLogin?.avatar})` }} />
                    }
                </Button>
            </Popover>
        }

    }

    const [suggestions, setSuggestions] = useState([]);
    const [searchValue, setSearchValue] = useState('');
    const history = useHistory();

    const handleSearchChange = debounce(async (e) => {
        const value = e.target.value;
        setSearchValue(value);
        if (value.trim() === '') {
            setSuggestions([]);
            return;
        }
        try {
            const res = await quanLyPhimService.searchPhim(value);
            setSuggestions(res.data.content || []);
        } catch {
            setSuggestions([]);
        }
    }, 300);


    return (
        <div>
            <header className="p-4 bg-gray-800 text-gray-100 fixed w-full bg-opacity-60 z-20">
                <div className="container flex justify-between h-16 mx-auto">
                    <NavLink rel="noopener noreferrer" to="/" aria-label="Back to homepage" className="flex items-center p-2">
                        <div className='d-flex' >
                            <img src='/img/logo.png' alt='logo' style={{ width: '50px', height: '100%' }} />
                            <img src='/img/name.png' alt='logo' style={{ width: '100px', height: '100%' }} />
                        </div>
                    </NavLink>
                    <ul className="items-stretch hidden space-x-3 lg:flex ml-20">
                        <li className="flex">
                            <NavLink to="/home" style={{ textDecoration: 'none' }} className="flex items-center font-medium -mb-0.5 border-b-2 px-4 border-transparent hover:text-violet-400" activeClassName="border-b-2 text-violet-400 border-violet-600">Trang Chủ</NavLink>
                        </li>
                        <li className="flex">
                            <NavLink to="/news" style={{ textDecoration: 'none' }} className="flex items-center font-medium -mb-0.5 border-b-2 px-4 border-transparent hover:text-violet-400" activeClassName="border-b-2 text-violet-400 border-violet-600">Tin Tức</NavLink>
                        </li>
                        <li className="flex">
                            <NavLink to="/contact" style={{ textDecoration: 'none' }} className="flex items-center font-medium -mb-0.5 border-b-2 px-4 border-transparent hover:text-violet-400" activeClassName="border-b-2 text-violet-400 border-violet-600">Liên Hệ</NavLink>
                        </li>

                    </ul>

                    <div className="items-center flex-shrink-0 hidden lg:flex">
                        <div style={{ position: 'relative' }}>
                            <Input
                                allowClear
                                placeholder="Tìm kiếm"
                                id='search'
                                className='rounded-full'
                                style={{ width: '300px' }}
                                prefix={<SearchOutlined />}
                                onChange={handleSearchChange}
                                onPressEnter={() => { /* Không làm gì khi nhấn Enter */ }}
                            />
                            {suggestions.length > 0 && (
                                <div className="absolute bg-white shadow-lg rounded w-full z-50 mt-1 max-h-80 overflow-y-auto" style={{ width: '300px' }}>
                                    {suggestions.map(phim => (
                                        <div
                                            key={phim.maPhim}
                                            className="flex items-center p-2 cursor-pointer hover:bg-gray-100"
                                            onClick={() => history.push(`/detail/${phim.maPhim}`)}
                                        >
                                            <img src={phim.hinhAnh} alt={phim.tenPhim} className="w-10 h-14 object-cover rounded mr-2" />
                                            <div>
                                                <div className="font-semibold" style={{ color: '#a78bfa' }}>{phim.tenPhim}</div>
                                                <div className="text-xs text-gray-500">{phim.moTa?.slice(0, 40)}...</div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                        {!_.isEmpty(userLogin) && (
                            <div style={{ marginLeft: '12px', marginRight: '12px' }}>
                                <NotificationBell />
                            </div>
                        )}
                        {renderLogin()}
                    </div>
                    <button className="p-4 lg:hidden">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" className="w-6 h-6 text-gray-100">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </header>

        </div>
    )
}
