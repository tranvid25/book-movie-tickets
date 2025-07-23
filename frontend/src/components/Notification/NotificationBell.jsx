import { useEffect, useState } from "react";
import axios from "axios";
import Echo from "laravel-echo";
import Pusher from "pusher-js";
import { toast } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";
import { TOKEN } from "../../util/settings/config";

const NotificationBell = () => {
  const [notifications, setNotifications] = useState([]);
  const [showDropdown, setShowDropdown] = useState(false);
  const accessToken = localStorage.getItem(TOKEN);

  // Lấy danh sách thông báo chưa đọc
  const fetchNotifications = async () => {
    if (!accessToken) return;
    try {
      const res = await axios.get(
        "http://localhost:8000/api/auth/notification/unread",
        {
          headers: { Authorization: `Bearer ${accessToken}` },
        }
      );
      setNotifications(res.data);
    } catch (error) {
      console.error("Lỗi khi lấy thông báo:", error);
    }
  };

  const markAsRead = async (id) => {
    if (!accessToken) return;
    try {
      await axios.put(
        `http://localhost:8000/api/auth/notification/${id}/read`,
        {},
        {
          headers: { Authorization: `Bearer ${accessToken}` },
        }
      );
      setNotifications((prev) => prev.filter((n) => n.id !== id)); // refresh danh sách
    } catch (error) {
      console.error("Lỗi khi đánh dấu đã đọc:", error);
    }
  };

  useEffect(() => {
    if (!accessToken) return;

    fetchNotifications();

    // Cấu hình Pusher
    window.Pusher = Pusher;
    const echo = new Echo({
      broadcaster: "pusher",
      key: "fbf58601d45c833fd72b",
      cluster: "ap1",
      forceTLS: true,
      authEndpoint: "http://localhost:8000/broadcasting/auth",
      auth: {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
      },
    });

    // Lắng nghe event realtime
    const channel = echo.channel("promotion-channel");
    channel.listen(".promotion-event", (data) => {
      toast.info(`🎬 Khuyến mãi mới: ${data.notification.title}`, {
        position: "top-right",
        autoClose: 5000,
      });
      fetchNotifications(); // refresh danh sách khi có thông báo mới
    });

    return () => {
      echo.leave("promotion-channel");
    };
  }, [accessToken]);

  return (
    <div style={{ position: "relative" }}>
      <button
        onClick={() => setShowDropdown(!showDropdown)}
        style={{
          fontSize: "24px",
          background: "none",
          border: "none",
          cursor: "pointer",
          padding: "8px",
        }}
      >
        🔔
      </button>

      {notifications.length > 0 && (
        <span
          style={{
            position: "absolute",
            top: 0,
            right: 0,
            background: "red",
            color: "white",
            borderRadius: "50%",
            padding: "2px 6px",
            fontSize: "12px",
            minWidth: "18px",
            textAlign: "center",
          }}
        >
          {notifications.length}
        </span>
      )}

      {/* Danh sách thông báo */}
      {showDropdown && (
        <div
          style={{
            position: "absolute",
            top: "45px",
            right: 0,
            background: "white",
            border: "1px solid #ddd",
            borderRadius: "8px",
            width: "350px",
            maxHeight: "400px",
            overflowY: "auto",
            zIndex: 1000,
            boxShadow: "0 4px 12px rgba(0,0,0,0.15)",
          }}
        >
          <div
            style={{
              padding: "12px 16px",
              borderBottom: "1px solid #eee",
              fontWeight: "bold",
              fontSize: "16px",
            }}
          >
            Thông báo ({notifications.length})
          </div>

          {notifications.length === 0 ? (
            <div
              style={{ padding: "20px", textAlign: "center", color: "#666" }}
            >
              Không có thông báo mới
            </div>
          ) : (
            notifications.map((n) => (
              <div
                key={n.id}
                style={{
                  padding: "12px 16px",
                  borderBottom: "1px solid #f0f0f0",
                  cursor: "pointer",
                  transition: "background-color 0.2s",
                }}
                onMouseEnter={(e) =>
                  (e.target.style.backgroundColor = "#f8f9fa")
                }
                onMouseLeave={(e) =>
                  (e.target.style.backgroundColor = "transparent")
                }
              >
                <div
                  style={{
                    fontWeight: "bold",
                    marginBottom: "4px",
                    color: "#333",
                  }}
                >
                  {n.title}
                </div>
                {n.description && (
                  <div
                    style={{
                      fontSize: "14px",
                      color: "#666",
                      marginBottom: "8px",
                      lineHeight: "1.4",
                    }}
                  >
                    {n.description}
                  </div>
                )}
                <div
                  style={{
                    display: "flex",
                    justifyContent: "space-between",
                    alignItems: "center",
                    fontSize: "12px",
                    color: "#999",
                  }}
                >
                  <span>{new Date(n.created_at).toLocaleString("vi-VN")}</span>
                  <button
                    onClick={(e) => {
                      e.stopPropagation();
                      markAsRead(n.id);
                    }}
                    style={{
                      background: "#007bff",
                      color: "white",
                      border: "none",
                      padding: "4px 8px",
                      borderRadius: "4px",
                      fontSize: "12px",
                      cursor: "pointer",
                    }}
                  >
                    Đã đọc
                  </button>
                </div>
              </div>
            ))
          )}
        </div>
      )}
    </div>
  );
};

export default NotificationBell;
