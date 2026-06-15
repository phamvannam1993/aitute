export function estimateDuration(text) {
    // Tính số ký tự (không tính khoảng trắng)
    const characterCount = text.replace(/\s+/g, '').length;

    // Tốc độ đọc tiếng Việt: 10 ký tự/giây
    const duration = characterCount / 10;

    // Làm tròn đến 1 chữ số thập phân
    return Math.round(duration * 10) / 10;
}
export async function getAudioDuration(url) {
    const audioContext = new (window.AudioContext || window.webkitAudioContext)();

    try {
      const response = await fetch(url);
      const arrayBuffer = await response.arrayBuffer();
      const audioBuffer = await audioContext.decodeAudioData(arrayBuffer);

      return audioBuffer.duration;
    } catch (error) {
      console.error('Error:', error);
      throw error;
    } finally {
      audioContext.close();
    }
  }
