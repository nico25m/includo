export default function MessageBubble({ role, text }) {
  return (
    <div className={`riga-messaggio ${role}`}>
      <div className="icona">
        {role === 'user' ? '👤' : '🤖'}
      </div>
      <div className={`fumetto ${role}`}>
        {text}
      </div>
    </div>
  );
}
