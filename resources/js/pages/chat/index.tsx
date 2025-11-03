import React from 'react';
import { Head, Link } from '@inertiajs/react';
import api from '../../lib/api';
import { ChatBubble, ChatReply, Product } from '../../types';
import QuickFilters from '@/components/quick-filters';
import ChatMessage from '@/components/chat-message';
import ProductList from '@/components/product-list';


export default function ChatIndex() {
  const [chatId, setChatId] = React.useState<number|undefined>(undefined);
  const [input, setInput] = React.useState('');
  const [sending, setSending] = React.useState(false);
  const [messages, setMessages] = React.useState<ChatBubble[]>([]);
  const [matched, setMatched] = React.useState<Product[]>([]);
  const inputRef = React.useRef<HTMLTextAreaElement|null>(null);
  const listRef = React.useRef<HTMLDivElement|null>(null);

  React.useEffect(() => {
    inputRef.current?.focus();
  }, []);

  React.useEffect(() => {
    listRef.current?.scrollTo({ top: listRef.current.scrollHeight, behavior: 'smooth' });
  }, [messages, matched]);

  const send = async (text: string) => {
    if (!text.trim() || sending) return;
    setSending(true);

    setMessages(prev => [...prev, { role: 'user', content: text }]);
    setInput('');

    try {
      const payload: any = { text };
      if (chatId) payload.chat_id = chatId;

      const { data } = await api.post<ChatReply>('/chat/send', payload);

      setChatId(data.chat_id);
      setMessages(prev => [...prev, { role: 'assistant', content: data.reply }]);
      setMatched(Array.isArray(data.matched_products) ? data.matched_products : []);
    } catch (e: any) {
      setMessages(prev => [...prev, {
        role: 'assistant',
        content: 'Xin lỗi, hiện không thể xử lý yêu cầu. Vui lòng thử lại sau.',
      }]);
    } finally {
      setSending(false);
      inputRef.current?.focus();
    }
  };

  const onKeyDown = (e: React.KeyboardEvent<HTMLTextAreaElement>) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      send(input);
    }
  };

  const pickFilter = (text: string) => {
    setInput(prev => prev ? `${prev} ${text}` : text);
    inputRef.current?.focus();
  };

  return (
    <div className="min-h-screen bg-gray-50 text-gray-900">
      <Head title="AI Chat Support" />
      <header className="bg-white border-b">
        <div className="max-w-5xl mx-auto p-4 flex justify-between">
          <Link href="/" className="font-semibold">AI Chat Support</Link>
          <nav className="space-x-4">
            <Link href="/" className="underline">Home</Link>
            <span className="text-gray-400">Chat</span>
          </nav>
        </div>
      </header>

      <main className="max-w-5xl mx-auto px-4 py-6">
        <div className="mb-4">
          <QuickFilters onPick={pickFilter} />
        </div>

        {/* Conversation */}
        <div ref={listRef} className="h-[60vh] overflow-y-auto bg-gray-100 border rounded-2xl p-4">
          {!messages.length && (
            <div className="text-sm text-gray-600">
                Getting Started:{' '}
                    <em>
                        “Tôi cần tai nghe chống ồn trong khoản giá tầm 300–400 của Sony”
                    </em>
            </div>
          )}
          {messages.map((m, idx) => (
            <ChatMessage key={idx} role={m.role} content={m.content} />
          ))}

          {matched?.length ? (
            <ProductList items={matched} />
          ) : null}
        </div>

        <div className="mt-4">
          <div className="rounded-2xl border bg-white p-3">
            <textarea
              ref={inputRef}
              value={input}
              onChange={(e) => setInput(e.target.value)}
              onKeyDown={onKeyDown}
              rows={3}
              placeholder="Nhập câu hỏi (Enter để gửi, Shift+Enter để xuống dòng)…"
              className="w-full outline-none resize-none"
            />
            <div className="mt-2 flex items-center justify-between">
              <div className="text-xs text-gray-500">
                {chatId ? `Chat #${chatId}` : 'Chat mới'}
              </div>
              <button
                disabled={sending || !input.trim()}
                onClick={() => send(input)}
                className={`px-4 py-2 rounded ${sending || !input.trim() ? 
                            'bg-gray-300 text-gray-600' : 
                            'bg-black text-white'}`}
              >
                {sending ? 'Sending…' : 'Sent'}
              </button>
            </div>
          </div>
        </div>
      </main>
    </div>
  );
}
