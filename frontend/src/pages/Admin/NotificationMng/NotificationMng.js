import React, { useState } from 'react';
import { Form, Input, Button, Card, message } from 'antd';
import axios from 'axios';
import { TOKEN, USER_LOGIN } from '../../../util/settings/config';
import { history } from '../../../App';

const { TextArea } = Input;

const NotificationMng = () => {
  const [loading, setLoading] = useState(false);
  const accessToken = localStorage.getItem(TOKEN);
  
  let userLogin = {};
  if (localStorage.getItem(USER_LOGIN)) {
    userLogin = JSON.parse(localStorage.getItem(USER_LOGIN));
  }

  if (!localStorage.getItem(TOKEN)) {
    history.replace('/');
  }

  if (userLogin.role !== 'Super' && userLogin.role !== 'QuanTri') {
    alert('Bạn không có quyền truy cập trang này!');
    history.replace('/');
  }

  const onFinish = async (values) => {
    setLoading(true);
    try {
      const response = await axios.post(
        'http://localhost:8000/api/auth/notification/create',
        {
          title: values.title,
          description: values.description
        },
        {
          headers: {
            Authorization: `Bearer ${accessToken}`,
            'Content-Type': 'application/json'
          }
        }
      );

      if (response.data.status === 200) {
        message.success('Tạo thông báo thành công!');
        // Reset form
        document.getElementById('notification-form').reset();
      }
    } catch (error) {
      console.error('Lỗi khi tạo thông báo:', error);
      message.error('Có lỗi xảy ra khi tạo thông báo!');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div style={{ padding: '20px' }}>
      <Card title="Tạo Thông Báo Mới" style={{ maxWidth: 600, margin: '0 auto' }}>
        <Form
          id="notification-form"
          layout="vertical"
          onFinish={onFinish}
        >
          <Form.Item
            label="Tiêu đề thông báo"
            name="title"
            rules={[
              { required: true, message: 'Vui lòng nhập tiêu đề thông báo!' },
              { max: 255, message: 'Tiêu đề không được quá 255 ký tự!' }
            ]}
          >
            <Input placeholder="Nhập tiêu đề thông báo..." />
          </Form.Item>

          <Form.Item
            label="Nội dung thông báo"
            name="description"
            rules={[
              { required: true, message: 'Vui lòng nhập nội dung thông báo!' }
            ]}
          >
            <TextArea 
              rows={4} 
              placeholder="Nhập nội dung thông báo..."
              maxLength={1000}
              showCount
            />
          </Form.Item>

          <Form.Item>
            <Button 
              type="primary" 
              htmlType="submit" 
              loading={loading}
              style={{ width: '100%' }}
            >
              Gửi Thông Báo
            </Button>
          </Form.Item>
        </Form>

        <div style={{ marginTop: '20px', padding: '15px', backgroundColor: '#f6f8fa', borderRadius: '6px' }}>
          <h4>📋 Hướng dẫn:</h4>
          <ul style={{ margin: '10px 0', paddingLeft: '20px' }}>
            <li>Thông báo sẽ được gửi đến tất cả người dùng đang online</li>
            <li>Người dùng sẽ nhận được toast notification ngay lập tức</li>
            <li>Thông báo sẽ hiển thị trong dropdown notification bell</li>
            <li>Người dùng có thể đánh dấu đã đọc từng thông báo</li>
          </ul>
        </div>
      </Card>
    </div>
  );
};

export default NotificationMng; 