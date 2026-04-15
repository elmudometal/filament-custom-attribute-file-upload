/* filepond-plugin-image-caption.js */
import { FileStatus } from 'filepond'

/**
 * FilePond image caption plugin
 *
 * FilePondPluginImageCaption 1.0.3
 * Author: clementmas
 * Licensed under MIT, https://opensource.org/licenses/MIT
 */
export default function ({ addFilter, utils }) {
    const { Type, createRoute, createView } = utils

    // Called when a new file is added
    addFilter('CREATE_VIEW', function (viewAPI) {
        const { is, view, query } = viewAPI

        // Make sure the option `addImageCaption` is enabled
        if (!query('GET_ADD_IMAGE_CAPTION')) return

        // Skip invalid file types
        if (!is('file')) return

        function onItemAdded({ root, props: { id } }) {
            const item = query('GET_ITEM', id)

            // Item could theoretically have been removed in the mean time
            if (!item || item.archived) return

            const value = item.getMetadata('caption')
            const uuid = item.getMetadata('uuid')

            const isInvalid = item.status === FileStatus.LOAD_ERROR

            // Append image caption input
            root.ref.imagePreview = view.appendChildView(
                view.createChildView(
                    createView(addCaptionInputField(value, isInvalid, uuid)),
                    {
                        id,
                    },
                ),
            )

            // Disable file action buttons tabindex (cancel, revert, etc.)
            // to easily tab from one caption input to another
            view.element
                .querySelectorAll('button')
                .forEach((button) => button.setAttribute('tabindex', -1))
        }

        view.registerWriter(
            createRoute({
                DID_INIT_ITEM: onItemAdded,
            }),
        )
    })

    // Plugin config options
    return {
        options: {
            // Enable or disable image captions
            addImageCaption: [true, Type.BOOLEAN],

            // Input placeholder
            imageCaptionPlaceholder: [null, Type.STRING],

            // Input max length
            imageCaptionMaxLength: [null, Type.INT],
        },
    }
}

// Create DOM input
function addCaptionInputField(value, isInvalid, uuid, id) {
    return {
        name: 'image-caption-input',
        tag: 'input',
        ignoreRect: true,
        create: function create({ root }) {
            // Input name
            //root.element.setAttribute('name', 'captions['+uuid+']');

            // Placeholder
            const placeholder = root.query('GET_IMAGE_CAPTION_PLACEHOLDER')
            if (placeholder) {
                root.element.setAttribute('placeholder', placeholder)
            }

            // Max length
            const maxLength = root.query('GET_IMAGE_CAPTION_MAX_LENGTH')
            if (maxLength) {
                root.element.setAttribute('maxlength', maxLength)
            }

            // Autocomplete off
            root.element.setAttribute('autocomplete', 'off')

            // Visually hide the element if the file is invalid but keep the input
            // to make sure the "captions[]" index will stay in sync with the FilePond photos
            if (isInvalid) {
                root.element.classList.add('image-caption-input-invalid')
            }

            // Prevent Enter key from submitting form
            root.element.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault()
                }
            })

            // Registrar la función para actualizar el estado
            if (uuid) {
                root.element.setAttribute(
                    'wire:model.defer',
                    `data.captions.${uuid}.caption`,
                )
            }

            if (!uuid) {
                root.element.setAttribute('disabled', 'disabled')
            }
            // Value
            if (value) {
                root.element.value = value
            }
        },
    }
}
