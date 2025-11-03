import RichText from './rich-text';

export default function ChatMessage({
    role,
    content,
}: {
    role: 'user' | 'assistant';
    content: string;
}) {
    const isUser = role === 'user';
    return (
        <div
            className={`flex ${isUser ? 'justify-end' : 'justify-start'} my-2`}
        >
            <div
                className={`${isUser ? 'bg-black text-white' : 'bg-white text-gray-900'} 
                            max-w-[80%] rounded-2xl border px-4 py-3 shadow 
                            ${isUser ? 'border-black' : 'border-gray-200'}`}
            >
                <RichText text={content} />
            </div>
        </div>
    );
}
