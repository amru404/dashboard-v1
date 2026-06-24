import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                vd: {
                    primary:        '#D4872D',   
                    secondary:      '#05142F',   
                    tertiary:       '#7A2BFF',   
                    neutral:        '#020307',   
                    surface:        '#05122B',   
                    'on-surface':   '#F7F7FF',   
                    card:     '#05122b',
                    'card-hover': '#071b33',
                    text:           '#FFFFFF',
                    muted:          'rgba(247,247,255,0.55)',
                    border:         'rgba(255,255,255,0.07)',
                    'border-strong':'rgba(255,255,255,0.20)',
                    'accent-cyan':  '#1EA7FF',
                    'accent-magenta':'#D93CFF',
                    error:          '#FF5A5F',
                    success:        '#22C55E',
                    warning:        '#F59E0B',
                },
            },
            fontFamily: {
                sans: ['"ans"', 'Inter', ...defaultTheme.fontFamily.sans],
            },
            fontSize: {
                'display': ['50px', { lineHeight: '57.5px', fontWeight: '700' }],
                'headline-lg': ['38px', { lineHeight: '40.32px', fontWeight: '700' }],
                'headline-md': ['28px', { lineHeight: '34px', fontWeight: '700' }],
                'headline-sm': ['21px', { lineHeight: '25px', fontWeight: '700' }],
                'body-lg':     ['18px', { lineHeight: '1.6' }],
                'body-md':     ['16px', { lineHeight: 'normal' }],
                'body-sm':     ['14px', { lineHeight: '1.5' }],
                'label-lg':    ['16px', { lineHeight: '1.2', fontWeight: '600' }],
                'label-md':    ['14px', { lineHeight: '1.2', fontWeight: '600' }],
                'label-sm':    ['12px', { lineHeight: '1.1', fontWeight: '600', letterSpacing: '0.08em' }],
                'eyebrow':     ['12px', { lineHeight: '1', fontWeight: '700', letterSpacing: '0.18em' }],
            },
            borderRadius: {
                'none': '0px',
                'sm':   '4px',
                'md':   '8px',
                'lg':   '18px',
                'xl':   '28px',
                'full': '9999px',
            },
            spacing: {
                'xs':  '10px',
                'sm':  '18px',
                'md':  '26px',
                'lg':  '40px',
                'xl':  '132px',
            },
            boxShadow: {
                'vd':    '0 1px 3px rgba(0,0,0,0.20), 0 4px 16px rgba(0,0,0,0.30)',
                'vd-lg': '0 20px 60px rgba(0,0,0,0.50)',
                'vd-glow-primary': '0 0 20px rgba(212,135,45,0.30)',
                'vd-glow-cyan':    '0 0 20px rgba(30,167,255,0.25)',
            },
            backgroundImage: {
                'vd-gradient': 'linear-gradient(135deg, #020307 0%, #05122B 50%, #05142F 100%)',
                'vd-hero':     'radial-gradient(ellipse 80% 60% at 50% 0%, rgba(122,43,255,0.20) 0%, transparent 70%)',
            },
        },
    },

    plugins: [forms],
};
