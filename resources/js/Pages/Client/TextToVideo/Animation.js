const animations = [
    'slide-in',
    'slide-in-right',
    'slide-in-top',
    'slide-in-bottom',
    'fade-in',
    // 'zoom-in',
    // 'rotate-in',
    'slide-fade-in',
]

export const randomAnimation = () => {
    return animations[Math.floor(Math.random() * animations.length)]
}
