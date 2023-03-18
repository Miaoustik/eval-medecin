import useInputGroupList from "./useInputGroupList";
import React, {useCallback, useMemo, useState} from "react";

export default function useInputGroupListWithSuggestions (inputNames = [], suggestionOn = '', suggestionsNotFiltred = {}) {
    const {inputs, handleChange, handleAdd, handleRemove, setInputs} = useInputGroupList(inputNames)

    const [suggestions, setSuggestions] = useState([])
    const [suggestionActive, setSuggestionActive] = useState(null);

    const filterSuggestions = useCallback((value) => {
        const suggestionsFiltred = JSON.parse(suggestionsNotFiltred).filter(e => {
            const regex = new RegExp('^' + value.charAt(0).toUpperCase() + value.slice(1))
            return regex.test(e.name)

        })
        setSuggestions(suggestionsFiltred)
    }, [suggestionsNotFiltred])

    const handleChange2 = useCallback((e) => {
        e.preventDefault()
        handleChange(e)
        const value = e.target.value
        console.log(value)
        filterSuggestions(value)
    }, [handleChange, filterSuggestions])

    const handleFocus = useCallback((e) => {
        setSuggestionActive(e.target.getAttribute('data-id'))
    }, [])

    const handleBlur = useCallback((e) => {

        setSuggestionActive(null)
    }, [])

    const handleMouseDown = useCallback((e) => {
        e.preventDefault()
    }, [])

    const handleClick = useCallback((e) => {
        e.preventDefault()
        const itemName = e.target.getAttribute('data-item')
        const inputId = e.target.getAttribute('data-id')
        setInputs((prevState) => {
            const newState = [...prevState]
            newState[inputId]['name'] = itemName
            return newState
        })
        filterSuggestions(itemName)
    }, [])


    return useMemo(() => ({
        inputs,
        handleAdd,
        'handleChange': handleChange2,
        handleBlur,
        handleFocus,
        suggestions,
        handleRemove,
        handleClick,
        handleMouseDown,
        suggestionActive,
        inputNames,
        suggestionOn
    }), [
        inputs,
        handleAdd,
        handleChange2,
        handleBlur,
        handleFocus,
        suggestions,
        handleRemove,
        handleClick,
        handleMouseDown,
        suggestionActive,
        inputNames,
        suggestionOn
    ])
}