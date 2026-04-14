import { useState, useEffect, useRef } from 'react'
import './App.css'

function App() {
  const [messaggi, setMessaggi] = useState([])
  const [testo, setTesto] = useState('')
  const [staCaricando, setStaCaricando] = useState(false)
  const [sessionId, setSessionId] = useState('')
  
  const fineMessaggi = useRef(null)

  useEffect(() => {
    let idGenerato = localStorage.getItem('includo_session')
    if (!idGenerato) {
      idGenerato = 'sessione-' + Math.random().toString(36).substr(2, 9)
      localStorage.setItem('includo_session', idGenerato)
    }
    setSessionId(idGenerato)

    setMessaggi([
      { role: 'assistant', content: 'Ciao! Sono Indo, il tuo assistente AI specializzato in orientamento formativo. Quali sono i tuoi interessi o le tue passioni nel mondo dell\'artigianato?' }
    ])
  }, [])

  useEffect(() => {
    if (fineMessaggi.current) {
      fineMessaggi.current.scrollIntoView({ behavior: 'smooth' })
    }
  }, [messaggi])

  const inviaMessaggio = async (e) => {
    e.preventDefault()
    if (!testo.trim() || staCaricando) return

    const copiaTesto = testo
    setTesto('')
    setMessaggi([...messaggi, { role: 'user', content: copiaTesto }])
    setStaCaricando(true)

    try {
      const risp = await fetch('http://localhost:8000/api/chat', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          session_id: sessionId,
          message: copiaTesto
        })
      })

      const datiRes = await risp.json()
      
      setMessaggi(vecchi => [...vecchi, { role: 'assistant', content: datiRes.reply }])
    } catch (errore) {
      console.log(errore)
      setMessaggi(vecchi => [...vecchi, { role: 'assistant', content: 'Spiacente, si è verificato un errore di connessione.' }])
    } finally {
      setStaCaricando(false)
    }
  }

  return (
    <div className="container-chat">
      <header className="titolo-app">
        <h1>IncluDO AI</h1>
        <p>L'intelligenza al servizio della tradizione</p>
      </header>

      <div className="lista-messaggi">
        {messaggi.map((m, i) => (
          <div key={i} className={'riga-messaggio ' + m.role}>
            <div className="icona">
              {m.role === 'user' ? '👤' : '🤖'}
            </div>
            <div className={'fumetto ' + m.role}>
              {m.content}
            </div>
          </div>
        ))}
        
        {staCaricando && (
          <div className="riga-messaggio assistant">
            <div className="icona">🤖</div>
            <div className="fumetto assistant">
              <span className="loading">Elaborazione in corso</span>
            </div>
          </div>
        )}
        <div ref={fineMessaggi} />
      </div>

      <form className="area-invio" onSubmit={inviaMessaggio}>
        <input 
          type="text" 
          placeholder="Scrivi qui il tuo messaggio..." 
          value={testo} 
          onChange={(e) => setTesto(e.target.value)}
        />
        <button type="submit" disabled={!testo.trim() || staCaricando}>Invia</button>
      </form>
    </div>
  )
}

export default App
