import { useState } from 'react';

export default function InputBox({ isLoading, onSend }) {
  const [text, setText] = useState("");

  function handleSubmit(e) {
    e.preventDefault();
    if (!text.trim() || isLoading) return;
    onSend(text);
    setText("");
  }

  return (
    <form className="area-invio" onSubmit={handleSubmit}>
      <input
        type="text"
        placeholder="Chiedi a Indo..."
        value={text}
        onChange={e => setText(e.target.value)}
      />
      <button type="submit" disabled={!text.trim() || isLoading}>Invia</button>
    </form>
  );
}
