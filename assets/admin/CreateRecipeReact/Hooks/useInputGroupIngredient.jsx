import useInputGroup from "./useInputGroup";
import {useCallback} from "react";

export default function (errorRef) {
    const {
        state,
        handleChange,
        handleAdd,
        handleRemove,
        setState
    } = useInputGroup(['quantity', 'name', 'error'])

    const handleBlur = useCallback((e) => {
        setState(prevState => {
            const message = "L'ingrédient éxiste déjà."
            const newState = [...prevState]

            //TODO FIX HERE

            newState.forEach((new1, index1) => {
                newState.forEach((new2, index2) => {
                    if (index1 !== index2) {
                        if ((new1.name.trim().toLowerCase() === new2.name.trim().toLowerCase())) {
                            if (newState[index1].error !== message) {
                                errorRef.current++
                                newState[index1].error = message
                            }
                        } else {
                            if (newState[index1].error === message) {
                                errorRef.current--
                                newState[index1].error = ''
                            }
                        }
                    }
                })
            })

            /*newState.forEach((el, index) => {
                if (el.name.trim().toLowerCase() === e.target.value.trim().toLowerCase()) {
                    console.log('yes')
                    if (newState[index].error !== message) {
                        errorRef.current++
                        newState[index].error = message
                    }
                } else {
                    console.log('no')
                    if (newState[index].error === message) {
                        errorRef.current--
                        newState[index].error = 'ok'
                    }
                }
            })*/
            return newState
        })
    }, [])

    return {
        state,
        handleChange,
        handleAdd,
        handleRemove,
        handleBlur,
        validationOn: 'name'
    }
}