export function useStreamChat() {
    const currentText = ref('')
    const isLoading = ref(false);
    const error = ref(null);

    const processStream = (url, messages) => {
        return new Promise((resolve, reject) => {
            const eventSource = new EventSource(url)
            currentText.value = '';
            isLoading.value = true;

            eventSource.onmessage = (event) => {
                try {
                    if (event.data === "<END_STREAMING_SSE>") {
                        eventSource.close()
                        isLoading.value = false;
                        resolve(currentText.value);
                        return
                    }

                    const data = JSON.parse(event.data);

                    if (data.error) {
                        error.value = data.error;
                        eventSource.close();
                        isLoading.value = false;
                        reject(data.error);
                        return
                    }

                    if (data.text) {
                        currentText.value += data.text
                    }
                } catch (e) {
                    console.error('Error parsing event data:', e)
                }
            }

            eventSource.onerror = (e) => {
                error.value = 'Connection error';
                eventSource.close();
                isLoading.value = false;
                reject(error.value)
            }
        })
    }

    return {
        currentText,
        isLoading,
        error,
        processStream
    }
}
