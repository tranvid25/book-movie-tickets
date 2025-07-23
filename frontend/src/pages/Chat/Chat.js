import React, { useEffect, useState, useRef } from "react";
import axios from "axios";
import Echo from "laravel-echo";
import Pusher from "pusher-js";
import { TOKEN } from "../../util/settings/config";

const Chat = () => {
  const [messages, setMessages] = useState([]);
  const [text, setText] = useState("");
  const [file, setFile] = useState(null);
  const chatEndRef = useRef(null);

  // Sử dụng state để lưu userId và accessToken, đồng bộ với localStorage
  const [userId, setUserId] = useState(Number(localStorage.getItem("userId")) || 0);
  const [accessToken, setAccessToken] = useState(localStorage.getItem(TOKEN));

  // Lắng nghe sự thay đổi của localStorage (ví dụ khi đăng nhập xong ở tab khác)
  useEffect(() => {
    const syncAuth = () => {
      setUserId(Number(localStorage.getItem("userId")) || 0);
      setAccessToken(localStorage.getItem(TOKEN));
    };
    window.addEventListener("storage", syncAuth);
    return () => window.removeEventListener("storage", syncAuth);
  }, []);

  // Nếu đăng nhập trong cùng tab, có thể cần đồng bộ lại sau khi login thành công
  // => Nếu app có context/redux, nên lấy từ đó thay vì localStorage

  // Lấy lịch sử chat khi load trang
  useEffect(() => {
    if (!accessToken) return;
    axios
      .get("http://localhost:8000/api/auth/messages", {
        headers: { Authorization: `Bearer ${accessToken}` },
      })
      .then((res) => {
        if (res.data && res.data.messages) {
          setMessages(
            res.data.messages.map((m) => ({
              from_id: m.userId,
              name: m.user?.name || "Ẩn danh",
              text: m.message,
              file_path: m.file_path || null,
            }))
          );
        }
      });
  }, [accessToken]);

  // Lắng nghe realtime group chat
  useEffect(() => {
    if (!userId || !accessToken) return;
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
    const channel = echo.join("chat");
    channel.listen(".UserOnline", (data) => {
      setMessages((prev) => [
        ...prev,
        {
          from_id: data.user.id,
          name: data.user.name,
          text: data.message,
          file_path: data.hinhAnhUrl || null,
        },
      ]);
    });

    return () => {
      echo.leave("chat");
    };
  }, [userId, accessToken]);

  useEffect(() => {
    chatEndRef.current?.scrollIntoView({ behavior: "smooth" });
  }, [messages]);

  const sendMessage = async () => {
    if (!text.trim() && !file) return;
    try {
      const formData = new FormData();
      if (text.trim()) formData.append("message", text);
      if (file) formData.append("file", file);
      await axios.post("http://localhost:8000/api/auth/messages", formData, {
        headers: {
          Authorization: `Bearer ${accessToken}`,
          "Content-Type": "multipart/form-data",
        },
      });
      setText("");
      setFile(null);
    } catch (err) {
      console.error("Gửi tin nhắn thất bại:", err);
    }
  };

  return (
    <div
      className="chat-container"
      style={{
        maxWidth: 500,
        margin: "40px auto",
        border: "1px solid #ccc",
        borderRadius: 8,
        padding: 16,
        backgroundColor: "#fff",
      }}
    >
      <h3 style={{ textAlign: "center" }}>💬 Chat nhóm</h3>
      <div
        className="chat-messages"
        style={{
          height: 400,
          overflowY: "auto",
          background: "#f1f1f1",
          padding: 12,
          marginBottom: 12,
          borderRadius: 6,
        }}
      >
        {messages.map((msg, idx) => {
          const isUser = msg.from_id === userId;
          return (
            <div
              key={idx}
              style={{
                display: "flex",
                justifyContent: isUser ? "flex-end" : "flex-start",
                marginBottom: 8,
              }}
            >
              <div
                style={{
                  maxWidth: "75%",
                  backgroundColor: isUser ? "#4CAF50" : "#e0e0e0",
                  color: isUser ? "#fff" : "#000",
                  borderRadius: 20,
                  padding: "10px 14px",
                  wordBreak: "break-word",
                }}
              >
                <strong>{msg.name}:</strong> {msg.text}
                {msg.file_path && (
                  <div style={{ marginTop: 6 }}>
                    {msg.file_path.match(/\.(jpg|jpeg|png|gif)$/i) ? (
                      <img
                        src={msg.file_path}
                        alt="file"
                        style={{
                          maxWidth: 180,
                          maxHeight: 180,
                          borderRadius: 8,
                          marginTop: 4,
                        }}
                      />
                    ) : (
                      <a
                        href={msg.file_path}
                        target="_blank"
                        rel="noopener noreferrer"
                        style={{
                          color: isUser ? "#fff" : "#007bff",
                          textDecoration: "underline",
                        }}
                      >
                        File đính kèm
                      </a>
                    )}
                  </div>
                )}
              </div>
            </div>
          );
        })}
        <div ref={chatEndRef} />
      </div>

      <div className="chat-input" style={{ display: "flex", gap: 8 }}>
        <input
          value={text}
          onChange={(e) => setText(e.target.value)}
          onKeyDown={(e) => e.key === "Enter" && sendMessage()}
          placeholder="Nhập tin nhắn..."
          style={{
            flex: 1,
            padding: 10,
            borderRadius: 4,
            border: "1px solid #ccc",
          }}
        />
        <input
          type="file"
          onChange={(e) => setFile(e.target.files[0])}
          style={{
            padding: "6px 0",
            borderRadius: 4,
            border: "1px solid #ccc",
            background: "#fff",
            width: 140,
          }}
        />
        <button
          onClick={sendMessage}
          style={{
            padding: "10px 16px",
            backgroundColor: "#007bff",
            color: "#fff",
            border: "none",
            borderRadius: 4,
            cursor: "pointer",
          }}
        >
          Gửi
        </button>
      </div>
    </div>
  );
};

export default Chat;
