import {useCallback, useMemo, useState} from "react";

export default function useInputCheckbox (items = []) {

    const itemsObj = items.map((v) => {
        return {
            'id' : v.id,
            'name' : v.name,
            'checked' : false
        }
    })

    const [inputs, setInputs ] = useState(itemsObj)

    const handleChange = useCallback((e) => {
        setInputs(prevState => {
            const newState = [...prevState]
            newState[e.target.getAttribute('data-index')]['checked'] = !newState[e.target.getAttribute('data-index')]['checked']
            return newState
        })
    }, [])


    return useMemo(() => ({
        inputs,
        handleChange,
        setInputs
    }), [items, inputs, handleChange, setInputs])
}