import { useEffect, useRef } from 'react';
import MessageBubble from './MessageBubble';

export default function MessageList({ messages, streamingMessage, isLoading }) {
  const bottomRef = useRef(null);

  useEffect(() => {
    bottomRef.current?.scrollIntoView({ behavior: "smooth" });
  }, [messages, streamingMessage, isLoading]);

  return (
    <div className="lista-messaggi">
      {messages.map((m, i) => (
        <MessageBubble key={i} role={m.role} text={m.text} />
      ))}

      {streamingMessage && (
        <MessageBubble role="assistant" text={streamingMessage} />
      )}

      {isLoading && !streamingMessage && (
        <div className="riga-messaggio assistant">
          <div className="icona">🤖</div>
          <div className="fumetto assistant">
            <span className="loading">Elaborazione in corso</span>
          </div>
        </div>
      )}
      <div ref={bottomRef} />
    </div>
  );
}
