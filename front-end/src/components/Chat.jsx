import { useState } from 'react';
import MessageList from './MessageList';
import InputBox from './InputBox';

export default function Chat() {
  const [messages, setMessages] = useState([
    { role: 'assistant', text: "Ciao! Sono Indo, il tuo assistente AI specializzato in orientamento formativo. Quali sono i tuoi interessi o le tue passioni nel mondo dell'artigianato?" }
  ]);
  const [isLoading, setIsLoading] = useState(false);
  const [streamingMessage, setStreamingMessage] = useState("");

  const [sessionId] = useState(() => {
    let idGenerato = localStorage.getItem('includo_session');
    if (!idGenerato) {
      idGenerato = 'sessione-' + crypto.randomUUID();
      localStorage.setItem('includo_session', idGenerato);
    }
    return idGenerato;
  });

  const addUserMessage = (text) => setMessages(prev => [...prev, { role: "user", text }]);
  const addAssistantMessage = (text) => setMessages(prev => [...prev, { role: "assistant", text }]);

  const sendMessage = async (userText) => {
    addUserMessage(userText);
    setIsLoading(true);

    try {
      const response = await fetch("http://localhost:8000/api/chat", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          session_id: sessionId,
          message: userText
        })
      });

      const contentType = response.headers.get("content-type");
      if (contentType && contentType.includes("application/json")) {
        const data = await response.json();
        addAssistantMessage(data.reply);
        return;
      }

      if (!response.ok) {
        throw new Error("Errore nella risposta del server");
      }

      const reader = response.body.getReader();
      const decoder = new TextDecoder();
      let botMessage = "";

      while (true) {
        const { done, value } = await reader.read();
        if (done) break;

        const chunk = decoder.decode(value, { stream: true });
        botMessage += chunk;
        setStreamingMessage(botMessage);
      }

      addAssistantMessage(botMessage);
      setStreamingMessage("");

    } catch (err) {
      console.error(err);
      addAssistantMessage('Spiacente, si è verificato un errore di connessione.');
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <>
      <header className="titolo-app">
        <h1>IncluDO</h1>
        <p>L’artigianato che include il futuro</p>
      </header>
      <MessageList messages={messages} streamingMessage={streamingMessage} isLoading={isLoading} />
      <InputBox isLoading={isLoading} onSend={sendMessage} />
    </>
  );
}
