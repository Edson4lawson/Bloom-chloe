import { onMounted, watch } from 'vue';

export function useSEO() {
  const updateMetaTags = (metadata) => {
    const { title, description, keywords, image, url } = metadata;

    if (title) {
      document.title = `${title} | Bloom by Chloé`;
      updateOrCreateMetaTag('og:title', title);
      updateOrCreateMetaTag('twitter:title', title);
    }

    if (description) {
      updateOrCreateMetaTag('description', description);
      updateOrCreateMetaTag('og:description', description);
      updateOrCreateMetaTag('twitter:description', description);
    }

    if (keywords) {
      updateOrCreateMetaTag('keywords', keywords);
    }

    if (image) {
      updateOrCreateMetaTag('og:image', image);
      updateOrCreateMetaTag('twitter:image', image);
    }

    if (url) {
      updateOrCreateMetaTag('og:url', url);
    }
  };

  const updateOrCreateMetaTag = (name, content) => {
    let element = document.querySelector(`meta[name="${name}"]`) || 
                  document.querySelector(`meta[property="${name}"]`);
    
    if (!element) {
      element = document.createElement('meta');
      if (name.startsWith('og:') || name.startsWith('twitter:')) {
        element.setAttribute('property', name);
      } else {
        element.setAttribute('name', name);
      }
      document.head.appendChild(element);
    }
    
    element.setAttribute('content', content);
  };

  return { updateMetaTags };
}
