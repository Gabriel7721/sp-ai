import React from 'react';

function linkify(text: string) {
  const urlRegex = /(https?:\/\/[^\s)]+)|(\bLink:\s*(https?:\/\/[^\s)]+))/gi;
  const parts: React.ReactNode[] = [];
  let lastIndex = 0;
  let match: RegExpExecArray | null;

  while ((match = urlRegex.exec(text)) !== null) {
    const start = match.index;
    if (start > lastIndex) {
      parts.push(text.slice(lastIndex, start));
    }
    const url = match[3] || match[1];
    if (url) {
      parts.push(
        <a key={start} 
            href={url} target="_blank" rel="noreferrer" 
            className="text-blue-600 underline break-all">
          {url}
        </a>
      );
    } else {
      parts.push(match[0]);
    }
    lastIndex = urlRegex.lastIndex;
  }
  if (lastIndex < text.length) parts.push(text.slice(lastIndex));

  return parts;
}

export default function RichText({ text }: { text: string }) {
  const chunks = text.split('\n');
  return (
    <div className="whitespace-pre-wrap leading-relaxed">
      {chunks.map((line, i) => (
        <span key={i}>
          {linkify(line)}
          {i < chunks.length - 1 ? <br /> : null}
        </span>
      ))}
    </div>
  );
}
